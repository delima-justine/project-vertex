<?php

namespace App\Http\Controllers;

use App\Models\AdminAudit;
use Illuminate\Http\Request;

class AdminAuditController extends Controller
{
    /**
     * Display a listing of the admin audits.
     */
    public function index(Request $request)
    {
        $limit = (int) $request->query('limit', 15);
        $limit = $limit > 0 ? min($limit, 100) : 15;

        $search = $request->query('search');
        $actionType = $request->query('action_type');
        $adminId = $request->query('admin_id');

        $audits = AdminAudit::with('admin')
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('admin_name', 'like', "%{$search}%")
                        ->orWhere('target_name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($actionType, function ($query, $actionType) {
                $query->where('action_type', $actionType);
            })
            ->when($adminId, function ($query, $adminId) {
                $query->where('admin_id', $adminId);
            })
            ->orderBy('performed_at', 'desc')
            ->paginate($limit);

        return response()->json($audits);
    }

    /**
     * Display the specified audit record.
     */
    public function show(AdminAudit $adminAudit)
    {
        return response()->json($adminAudit->load('admin'));
    }
}
