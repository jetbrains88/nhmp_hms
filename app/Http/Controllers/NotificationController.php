<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display all notifications for the user
     */
    public function index(Request $request)
    {
        $query = Notification::where('user_id', auth()->id())
            ->with(['triggeredBy', 'related'])
            ->orderBy('created_at', 'desc');

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('unread')) {
            $query->whereNull('read_at');
        }

        $notifications = $query->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Get unread notification count for the user
     */
    public function getUnreadCount()
    {
        $count = $this->notificationService->getUnreadCount(auth()->user());

        return response()->json(['count' => $count]);
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead(Notification $notification)
    {
        if ((int) $notification->user_id !== (int) auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $this->notificationService->markAsRead($notification);

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back();
    }

    /**
     * Mark all notifications as read for the user
     */
    public function markAllRead()
    {
        $count = $this->notificationService->markAllAsRead(auth()->user());

        if (request()->wantsJson()) {
            return response()->json(['marked' => $count]);
        }

        return redirect()->back()->with('success', 'All notifications marked as read');
    }

    /**
     * Delete a notification
     */
    public function destroy(Notification $notification)
    {
        if ((int) $notification->user_id !== (int) auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->delete();

        return redirect()->back()->with('success', 'Notification deleted');
    }

    /**
     * Get notifications for API (used in dropdown)
     */
    public function getLatest()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->with(['triggeredBy'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'body' => $notification->body,
                    'type' => $notification->type,
                    'read' => !is_null($notification->read_at),
                    'created_at' => $notification->created_at->diffForHumans(),
                    'action_url' => $notification->action_url,
                    'action_text' => $notification->action_text
                ];
            });

        return response()->json($notifications);
    }
}
