<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display all notifications for the user.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get all notifications (read and unread), paginated
        $notifications = $user->notifications()->paginate(15);
        
        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark a specific notification as read and redirect.
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->firstOrFail();
        
        // Mark as read
        $notification->markAsRead();
        
        // Safely redirect based on the data structure of your notification
        if (isset($notification->data['url'])) {
            return redirect($notification->data['url']);
        } elseif (isset($notification->data['task_id'])) {
            return redirect()->route('tasks.show', $notification->data['task_id']);
        }
        
        // Fallback if no URL is provided in the notification data
        return back();
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        
        return back()->with('status', 'All notifications have been marked as read.');
    }
}