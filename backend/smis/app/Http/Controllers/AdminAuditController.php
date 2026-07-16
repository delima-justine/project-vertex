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
        $timePeriod = $request->query('time_period');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $now = now();

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
            ->when($timePeriod, function ($query, $timePeriod) use ($now, $startDate, $endDate) {
                if ($timePeriod === 'today') {
                    $query->whereDate('performed_at', $now->toDateString());
                } elseif ($timePeriod === 'week') {
                    $query->where('performed_at', '>=', $now->copy()->subDays(6)->startOfDay())
                          ->where('performed_at', '<=', $now);
                } elseif ($timePeriod === 'month') {
                    $query->whereMonth('performed_at', $now->month)
                          ->whereYear('performed_at', $now->year);
                } elseif ($timePeriod === 'custom') {
                    if ($startDate) {
                        $query->where('performed_at', '>=', \Carbon\Carbon::parse($startDate)->startOfDay());
                    }
                    if ($endDate) {
                        $query->where('performed_at', '<=', \Carbon\Carbon::parse($endDate)->endOfDay());
                    }
                }
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
