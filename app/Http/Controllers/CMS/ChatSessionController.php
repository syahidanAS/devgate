<?php

namespace App\Http\Controllers\CMS;

use App\Events\NewChatMessage;
use App\Http\Controllers\Controller;
use App\Models\ChatSession;
use App\Models\ChatSessionMessage;
use App\Notifications\ChatReplyNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class ChatSessionController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $query = ChatSession::query()->orderBy('updated_at', 'desc');

        if (!$user->hasRole('superadmin')) {
            $categories = [];
            if ($user->hasRole('admin-marketplace')) {
                $categories[] = 'marketplace';
            }
            if ($user->hasRole('author')) {
                $categories[] = 'collaboration';
            }
            // Project category is only for superadmin

            if (empty($categories)) {
                abort(403, 'Anda tidak memiliki akses ke fitur chat.');
            }
            $query->whereIn('category', $categories);
        }

        $sessions = $query->get();

        return view('cms.chat.index', compact('sessions'));
    }

    public function fetchMessages($sessionId)
    {
        $session = $this->getAuthorizedSession($sessionId);
        $messages = $session->messages()->with('sender:id,name')->orderBy('created_at', 'asc')->get();
        return response()->json(['messages' => $messages]);
    }

    public function reply(Request $request, $sessionId)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $session = $this->getAuthorizedSession($sessionId);

        $message = $session->messages()->create([
            'sender_type' => 'admin',
            'sender_id' => Auth::id(),
            'message' => $request->message,
        ]);

        // Broadcast to customer
        broadcast(new NewChatMessage($message))->toOthers();

        // Send Email Notification
        $email = $session->user_id ? $session->user->email : $session->guest_email;
        if ($email) {
            Notification::route('mail', $email)->notify(new ChatReplyNotification($message));
        }

        // Update session updated_at to bring it to top
        $session->touch();

        return response()->json(['message' => $message->load('sender:id,name')]);
    }

    private function getAuthorizedSession($sessionId)
    {
        $session = ChatSession::findOrFail($sessionId);
        $user = Auth::user();

        if (!$user->hasRole('superadmin')) {
            if ($session->category === 'marketplace' && !$user->hasRole('admin-marketplace')) {
                abort(403);
            }
            if ($session->category === 'collaboration' && !$user->hasRole('author')) {
                abort(403);
            }
            if ($session->category === 'project') {
                abort(403);
            }
        }

        return $session;
    }
}
