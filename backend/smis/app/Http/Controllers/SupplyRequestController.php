<?php

namespace App\Http\Controllers;

use App\Models\SupplyRequest;
use Illuminate\Http\Request;

class SupplyRequestController extends Controller
{
    // Returns request with related user and supply data
    public function index(Request $request)
    {
        $user = $request->user();
        $query = SupplyRequest::with(['user.office', 'supply.category', 'supply.unit']);

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

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:tbl_user,id',
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
            'approved_by' => 'required_if:status,approved|exists:tbl_user,id',
            'quantity_req' => 'sometimes|integer|min:1',
        ]);

        $supply_request->update($validated);
        return response()->json($supply_request);
    }

    // Delete a supply request
    public function destroy(SupplyRequest $supply_request)
    {
        $supply_request->delete();
        return response()->json(['message' => 'Supply request deleted successfully']);
    }
}
