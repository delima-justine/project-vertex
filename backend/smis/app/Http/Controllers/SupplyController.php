<?php

namespace App\Http\Controllers;

use App\Models\Supply;
use App\Models\AdminAudit;
use App\Models\Archive;
use App\Models\SupplyRequest;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SupplyController extends Controller
{
    // List all supplies
    public function index() 
    {
        return response()->json(Supply::with(['category', 'unit'])->get());
    }

    /**
     * Get stock history for a specific supply.
     */
    public function history(Supply $supply)
    {
        $history = [];

        // 1. Get audits where this supply was the target (Manual updates, Create, etc.)
        $supplyAudits = AdminAudit::where('target_type', Supply::class)
            ->where('target_id', $supply->stock_num)
            ->get();

        foreach ($supplyAudits as $audit) {
            $oldQty = $audit->old_values['quantity'] ?? null;
            $newQty = $audit->new_values['quantity'] ?? null;
            $change = null;
            
            if ($oldQty !== null && $newQty !== null) {
                $change = $newQty - $oldQty;
            } elseif ($audit->action_type === 'CREATE') {
                $change = $newQty;
                $oldQty = 0;
            }

            // Extract request ID if present in description (e.g. "Released Request #123")
            $reqNum = null;
            if (preg_match('/Request #(\d+)/i', $audit->description, $matches)) {
                $reqNum = $matches[1];
            }

            $history[] = [
                'date_time' => $audit->performed_at,
                'action' => $audit->action_type,
                'change' => $change !== null ? ($change > 0 ? "+$change" : (string)$change) : null,
                'req_approve_num' => $reqNum,
                'prev_qty' => $oldQty,
                'new_qty' => $newQty,
                'performed_by' => $audit->admin_name,
                'details' => $audit->description,
            ];
        }

        // 2. Get audits where SupplyRequest was the target, and it relates to this supply
        $requestIds = SupplyRequest::where('supply_id', $supply->stock_num)->pluck('id')->toArray();
        
        $requestAudits = AdminAudit::where('target_type', SupplyRequest::class)
            ->whereIn('target_id', $requestIds)
            ->get();

        foreach ($requestAudits as $audit) {
            // Only add if it's not a covered supply update (to avoid duplicate stock change entries)
            // But we want to show request status changes.
            $history[] = [
                'date_time' => $audit->performed_at,
                'action' => $audit->action_type . ' (Request)',
                'change' => null,
                'req_approve_num' => $audit->target_id,
                'prev_qty' => null,
                'new_qty' => null,
                'performed_by' => $audit->admin_name,
                'details' => $audit->description,
            ];
        }

        // 3. Get Archives for requests related to this supply
        $archives = Archive::where('table_name', 'tbl_request')
            ->whereIn('original_id', $requestIds)
            ->get();

        foreach ($archives as $archive) {
            $history[] = [
                'date_time' => $archive->archived_at,
                'action' => 'ARCHIVE',
                'change' => null,
                'req_approve_num' => $archive->original_id,
                'prev_qty' => null,
                'new_qty' => null,
                'performed_by' => $archive->archiver ? ($archive->archiver->first_name . ' ' . $archive->archiver->last_name) : 'System',
                'details' => "Supply request archived",
            ];
        }

        // Sort by date_time descending
        usort($history, function ($a, $b) {
            return $b['date_time'] <=> $a['date_time'];
        });

        return response()->json($history);
    }

    // Save a new supply
    public function store(Request $request)
    {
        $validated = $request->validate([
            'stock_num' => 'required|string|unique:tbl_supply,stock_num',
            'item_desc' => 'required|string',
            'quantity' => 'required|integer',
            'category_id' => 'required|integer',
            'unit_id' => 'required|integer',
            'status' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        $supply = Supply::create($validated);
        
        AuditService::log('CREATE', $supply, "Created new supply: {$supply->item_desc}", null, $supply->toArray());

        return response()->json($supply, 201);
    }

    // Show a specific supply
    public function show(Supply $supply)
    {
        return response()->json($supply);
    }

    // Update a supply
    public function update(Request $request, Supply $supply)
    {
        $validated = $request->validate([
            'stock_num' => [
                'required',
                'string',
                Rule::unique('tbl_supply', 'stock_num')->ignore($supply->stock_num, 'stock_num'),
            ],
            'item_desc' => 'required|string',
            'quantity' => 'required|integer',
            'category_id' => 'required|integer',
            'unit_id' => 'required|integer',
            'status' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        $oldValues = $supply->toArray();
        $supply->update($validated);

        AuditService::log('UPDATE', $supply, "Updated supply: {$supply->item_desc}", $oldValues, $supply->fresh()->toArray());

        return response()->json($supply);
    }

    // Delete a supply
    public function destroy(Supply $supply)
    {
        $oldValues = $supply->toArray();
        $supply->delete();

        AuditService::log('DELETE', $supply, "Deleted supply: {$supply->item_desc}", $oldValues);

        return response()->json(['message' => 'Supply deleted successfully']);
    }
}
