<?php

namespace App\Http\Controllers;

use App\Events\NotificationSent;
use App\Models\SupplyRequest;
use App\Models\Notification;
use App\Services\AuditService;
use App\Mail\SupplyRequestSlip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SupplyRequestController extends Controller
{
    // Returns request with related user and supply data
    public function index(Request $request)
    {
        $user = $request->user();
        $query = SupplyRequest::with(['user.office', 'supply.category', 'supply.unit', 'approver'])
            ->whereDoesntHave('archive');

        // Check user role
        $role = strtolower($user->role->role_name ?? '');

        if ($role === 'user') {
            // Regular users only see their own requests
            $query->where('user_id', $user->id);
        } elseif ($request->has('user_id')) {
            // Admins/SuperAdmins can filter by user_id if provided
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->get('per_page', 100); // Higher default for batching to work better, or we can handle grouping better later
        return response()->json($query->paginate($perPage));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:tbl_user,id',
            'batch_id' => 'nullable|string|max:100',
            'supply_id' => 'required|exists:tbl_supply,stock_num',
            'quantity_req' => 'required|integer|min:1',
            'purpose' => 'nullable|string|max:255',
        ]);

        // Default status to 'pending'
        $supply_request = SupplyRequest::create($validated);

        return response()->json($supply_request, 201);
    }

    public function storeBatch(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:tbl_user,id',
            'batch_id' => 'nullable|string|max:100',
            'items' => 'required|array',
            'items.*.supply_id' => 'required|exists:tbl_supply,stock_num',
            'items.*.quantity_req' => 'required|integer|min:1',
            'purpose' => 'nullable|string|max:255',
        ]);

        $createdRequests = [];
        foreach ($validated['items'] as $item) {
            $sr = SupplyRequest::create([
                'user_id' => $validated['user_id'],
                'batch_id' => $validated['batch_id'],
                'supply_id' => $item['supply_id'],
                'quantity_req' => $item['quantity_req'],
                'purpose' => $validated['purpose'],
                'status' => 'pending'
            ]);
            $createdRequests[] = $sr->load(['user', 'supply']);
        }

        if (count($createdRequests) > 0) {
            try {
                $user = $createdRequests[0]->user;
                Mail::to($user->email)->send(new SupplyRequestSlip(collect($createdRequests), 'pending'));
            } catch (\Exception $e) {
                Log::error("Failed to send pending request email: " . $e->getMessage());
            }
        }

        return response()->json(['message' => 'Batch request created successfully', 'data' => $createdRequests], 201);
    }

    // Returns request with related user, supply, and approver data
    public function show(SupplyRequest $supply_request)
    {
        return response()->json($supply_request->load([
            'user.office', 
            'user.role', 
            'supply.category', 
            'supply.unit', 
            'approver.role'
        ]));
    }

    // Update request status and approver
    public function update(Request $request, SupplyRequest $supply_request)
    {
        $validated = $request->validate([
            'status' => 'sometimes|in:pending,approved,released,disapproved',
            'approved_by' => 'nullable|exists:tbl_user,id',
            'quantity_req' => 'sometimes|integer|min:1',
        ]);

        if (isset($validated['status']) && $validated['status'] === 'approved' && !isset($validated['approved_by'])) {
            return response()->json(['message' => 'The approved_by field is required when status is approved.'], 422);
        }

        if (isset($validated['status']) && $validated['status'] === 'disapproved') {
            $validated['approved_by'] = null;
        }

        $oldValues = $supply_request->toArray();
        $supply_request->update($validated);

        // Send email if status is changed
        if (isset($validated['status']) && $validated['status'] !== ($oldValues['status'] ?? '')) {
            try {
                // Ensure user and supply are loaded for the email
                $supply_request->load(['user', 'supply']);
                Mail::to($supply_request->user->email)->send(new SupplyRequestSlip(collect([$supply_request]), $supply_request->status));
            } catch (\Exception $e) {
                Log::error("Failed to send status update email: " . $e->getMessage());
            }
        }

        // Deduct stock if status is changed to 'released'
        if (isset($validated['status']) && $validated['status'] === 'released' && ($oldValues['status'] ?? '') !== 'released') {
            $supply = $supply_request->supply;
            if ($supply) {
                $oldSupplyValues = $supply->toArray();
                $supply->quantity -= $supply_request->quantity_req;
                
                // Prevent negative quantity
                if ($supply->quantity < 0) {
                    $supply->quantity = 0;
                }

                $supply->save();

                // Log the stock update on the supply model
                AuditService::log(
                    'UPDATE', 
                    $supply, 
                    "Deducted stock due to released Request #{$supply_request->id}", 
                    $oldSupplyValues, 
                    $supply->fresh()->toArray()
                );
            }
        }

        AuditService::log('UPDATE', $supply_request, "Updated supply request status to: {$supply_request->status}", $oldValues, $supply_request->fresh()->toArray());

        $notif = Notification::create([
            'user_id' => $supply_request->user_id,
            'request_id' => $supply_request->id,
            'batch_id' => $supply_request->batch_id,
            'message' => "Your request for {$supply_request->supply_id} has been {$supply_request->status}.",
            'action' => $supply_request->status,
        ]);

        broadcast(new NotificationSent($notif));

        return response()->json($supply_request);
    }

    // Delete a supply request
    public function destroy(SupplyRequest $supply_request)
    {
        $oldValues = $supply_request->toArray();
        $supply_request->delete();

        AuditService::log('DELETE', $supply_request, "Deleted supply request for: {$supply_request->supply_id}", $oldValues);

        return response()->json(['message' => 'Supply request deleted successfully']);
    }

    public function statusCounts(Request $request)
    {
        $user = $request->user();
        $query = SupplyRequest::whereDoesntHave('archive');

        $role = strtolower($user->role->role_name ?? '');

        if ($role === 'user') {
            $query->where('user_id', $user->id);
        }

        $counts = $query->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return response()->json([
            'pending' => (int)($counts['pending'] ?? 0),
            'approved' => (int)($counts['approved'] ?? 0),
            'released' => (int)($counts['released'] ?? 0),
            'disapproved' => (int)($counts['disapproved'] ?? 0),
        ]);
    }

    // Update multiple requests in a batch
    public function updateBatch(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:tbl_request,id',
            'items.*.quantity_req' => 'nullable|integer|min:0',
            'status' => 'required|in:pending,approved,released,disapproved',
            'approved_by' => 'nullable|exists:tbl_user,id',
        ]);

        $updatedRequests = [];
        $affectedBatches = [];

        foreach ($validated['items'] as $itemData) {
            $sr = SupplyRequest::find($itemData['id']);
            if (!$sr) continue;

            $oldValues = $sr->toArray();
            $newQuantity = $itemData['quantity_req'] ?? $sr->quantity_req;

            // Deduct stock if status is changed to 'released'
            if ($validated['status'] === 'released' && $sr->status !== 'released') {
                $supply = $sr->supply;
                if ($supply) {
                    $oldSupplyValues = $supply->toArray();
                    $supply->quantity -= $newQuantity;
                    if ($supply->quantity < 0) $supply->quantity = 0;
                    $supply->save();
                    AuditService::log('UPDATE', $supply, "Deducted stock due to released Request #{$sr->id}", $oldSupplyValues, $supply->fresh()->toArray());
                }
            }

            $sr->update([
                'status' => $validated['status'],
                'approved_by' => $validated['approved_by'] ?? $sr->approved_by,
                'quantity_req' => $newQuantity
            ]);

            AuditService::log('UPDATE', $sr, "Batch updated supply request status to: {$sr->status}", $oldValues, $sr->fresh()->toArray());
            
            // Track affected batches for grouped notifications
            $batchKey = $sr->batch_id ?? 'single-' . $sr->id;
            if (!isset($affectedBatches[$batchKey])) {
                $affectedBatches[$batchKey] = [
                    'user_id' => $sr->user_id,
                    'count' => 0,
                    'first_supply' => $sr->supply_id,
                    'first_id' => $sr->id,
                    'batch_id' => $sr->batch_id
                ];
            }
            $affectedBatches[$batchKey]['count']++;
            
            $updatedRequests[] = $sr->load(['user', 'supply']);
        }

        // Send grouped notifications
        foreach ($affectedBatches as $key => $batchData) {
            $isBatch = !str_starts_with($key, 'single-');
            $status = $validated['status'];

            if ($isBatch && $batchData['count'] > 1) {
                $message = "Your batch request with {$batchData['count']} items has been {$status}.";
            } else {
                $message = "Your request for {$batchData['first_supply']} has been {$status}.";
            }

            $notif = Notification::create([
                'user_id' => $batchData['user_id'],
                'batch_id' => $batchData['batch_id'],
                'request_id' => $isBatch ? null : $batchData['first_id'],
                'message' => $message,
                'action' => $status,
            ]);
            broadcast(new NotificationSent($notif));
        }

        if (count($updatedRequests) > 0) {
            try {
                $user = $updatedRequests[0]->user;
                Mail::to($user->email)->send(new SupplyRequestSlip(collect($updatedRequests), $validated['status']));
            } catch (\Exception $e) {
                Log::error("Failed to send batch status email: " . $e->getMessage());
            }
        }

        return response()->json(['message' => 'Batch updated successfully', 'count' => count($updatedRequests)]);
    }
}
