<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Return unread notifications for the authenticated user as JSON.
     * Called by the public bell Alpine.js component.
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['count' => 0, 'notifications' => []], 401);
        }

        $notifications = $user->unreadNotifications()
            ->latest()
            ->take(15)
            ->get()
            ->map(fn ($n) => [
                'id'         => $n->id,
                'type'       => $n->data['type'] ?? 'general',
                'data'       => $n->data,
                'created_at' => $n->created_at->diffForHumans(),
                'url'        => $n->data['url'] ?? '#',
            ]);

        return response()->json([
            'count'         => $user->unreadNotifications()->count(),
            'notifications' => $notifications,
        ]);
    }

    /**
     * Mark a single notification as read and redirect to its URL.
     */
    public function markRead(string $id): RedirectResponse
    {
        $notification = Auth::user()
            ->notifications()
            ->findOrFail($id);

        $notification->markAsRead();

        $url = $notification->data['url'] ?? route('blog.index');

        return redirect($url);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllRead(): JsonResponse
    {
        $user = Auth::user();
        if ($user) {
            $user->unreadNotifications()->update(['read_at' => now()]);
        }

        return response()->json(['status' => 'ok']);
    }
}
