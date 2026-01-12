<?php

namespace App\Http\Controllers\Api;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get all notifications for the authenticated user
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $notifications = Notification::where('user_id', $user->user_id)
            ->orderBy('send_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $notifications,
            'unread_count' => $notifications->where('is_read', false)->count(),
        ]);
    }

    /**
     * Mark a specific notification as read
     */
    public function read($id)
    {
        $notification = Notification::findOrFail($id);
        
        // Check if user owns this notification
        if ($notification->user_id !== Auth::user()->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $notification->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
            'data' => $notification,
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function readAll()
    {
        $user = Auth::user();
        
        Notification::where('user_id', $user->user_id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read',
        ]);
    }

    /**
     * Get unread notification count
     */
    public function unreadCount()
    {
        $user = Auth::user();
        
        $count = Notification::where('user_id', $user->user_id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success' => true,
            'unread_count' => $count,
        ]);
    }
}
