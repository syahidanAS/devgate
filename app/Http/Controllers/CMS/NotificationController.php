<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Return unread notifications for the authenticated user as JSON.
     * Called every 30s by the CMS bell Alpine.js component.
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();

        $notifications = $user->unreadNotifications()
            ->latest()
            ->take(20)
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

        $url = $notification->data['url'] ?? route('cms.dashboard');

        return redirect($url);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllRead(): JsonResponse
    {
        Auth::user()->unreadNotifications()->update(['read_at' => now()]);

        return response()->json(['status' => 'ok']);
    }
}
