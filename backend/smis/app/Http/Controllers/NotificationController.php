<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // List all notifications for the authenticated user
    public function index(Request $request) 
    {
        $query = Notification::with(['user', 'office', 'supplyRequest'])
            ->where('user_id', $request->user()->id);

        if ($request->has('tab') && $request->tab != 'all') {
            switch ($request->tab) {
                case 'unread':
                    $query->whereNull('read_at');
                    break;
                case 'approved':
                    $query->whereIn('action', ['approved', 'released']);
                    break;
                case 'system':
                    $query->whereIn('action', ['low stock', 'out of stock', 'pending']);
                    break;
                case 'denied':
                    $query->where('action', 'disapproved');
                    break;
            }
        }

        if ($request->has('office_id') && $request->office_id != '') {
            $query->where('office_id', $request->office_id);
        }

        if ($request->has('from_date') && $request->from_date != '') {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date') && $request->to_date != '') {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $notifications = $query->orderBy('created_at', 'desc')
            ->paginate(5);

        return response()->json($notifications);
    }

    // Mark a notification as read
    public function markAsRead(Notification $notification)
    {
        $notification->update(['read_at' => now()]);
        return response()->json(['message' => 'Notification marked as read']);
    }

    // Mark all notifications as read for the authenticated user
    public function markAllAsRead(Request $request)
    {
        Notification::where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['message' => 'All notifications marked as read']);
    }

    // Delete a notification
    public function destroy(Notification $notification)
    {
        $notification->delete();
        return response()->json(['message' => 'Notification deleted successfully']);
    }
}
