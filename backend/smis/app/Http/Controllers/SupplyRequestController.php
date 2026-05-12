<?php

namespace App\Http\Controllers;

use App\Events\NotificationSent;
use App\Models\SupplyRequest;
use App\Models\Notification;
use App\Services\AuditService;
use App\Mail\RequestDisapproved;
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

        // Send email if status is changed to 'disapproved'
        if (isset($validated['status']) && $validated['status'] === 'disapproved' && ($oldValues['status'] ?? '') !== 'disapproved') {
            try {
                // Ensure user and supply are loaded for the email
                $supply_request->load(['user', 'supply']);
                Mail::to($supply_request->user->email)->send(new RequestDisapproved(collect([$supply_request])));
            } catch (\Exception $e) {
                Log::error("Failed to send disapproval email: " . $e->getMessage());
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

    // Update multiple requests in a batch
    public function updateBatch(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:tbl_request,id',
            'status' => 'required|in:pending,approved,released,disapproved',
            'approved_by' => 'nullable|exists:tbl_user,id',
        ]);

        $requests = SupplyRequest::whereIn('id', $validated['ids'])->get();
        $updatedRequests = [];

        foreach ($requests as $sr) {
            $oldValues = $sr->toArray();
            $sr->update([
                'status' => $validated['status'],
                'approved_by' => $validated['approved_by'] ?? $sr->approved_by,
            ]);

            AuditService::log('UPDATE', $sr, "Batch updated supply request status to: {$sr->status}", $oldValues, $sr->fresh()->toArray());

            $notif = Notification::create([
                'user_id' => $sr->user_id,
                'request_id' => $sr->id,
                'message' => "Your request for {$sr->supply_id} has been {$sr->status}.",
                'action' => $sr->status,
            ]);
            broadcast(new NotificationSent($notif));
            
            $updatedRequests[] = $sr->load(['user', 'supply']);
        }

        if ($validated['status'] === 'disapproved' && count($updatedRequests) > 0) {
            try {
                $user = $updatedRequests[0]->user;
                Mail::to($user->email)->send(new RequestDisapproved(collect($updatedRequests)));
            } catch (\Exception $e) {
                Log::error("Failed to send batch disapproval email: " . $e->getMessage());
            }
        }

        return response()->json(['message' => 'Batch updated successfully', 'count' => count($updatedRequests)]);
    }
}
