<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // List all notifications
    public function index(Request $request) 
    {
        $notifications = Notification::with(['user', 'office', 'supplyRequest'])
            ->orderBy('created_at', 'desc')
            ->get();

            return response()->json($notifications);
    }

    // Mark a notification as read
    public function markAsRead(Notification $notification)
    {
        $notification->update(['read_at' => now()]);
        return response()->json(['message' => 'Notification marked as read']);
    }

    // Mark all notifications as read for a user
    public function markAllAsRead(Request $request)
    {
        $user_id = $request->user_id;
        Notification::where('user_id', $user_id)
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
