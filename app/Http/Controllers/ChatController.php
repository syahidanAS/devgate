<?php

namespace App\Http\Controllers;

use App\Events\NewChatMessage;
use App\Models\ChatSession;
use App\Models\ChatSessionMessage;
use App\Notifications\AdminTelegramChatNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function startSession(Request $request)
    {
        $request->validate([
            'category' => 'required|in:marketplace,project,collaboration',
        ]);

        if (!Auth::check()) {
            $request->validate([
                'guest_name' => 'required|string|max:255',
                'guest_email' => 'required|email|max:255',
                'guest_whatsapp' => 'required|string|max:20',
            ]);
        }

        $session = ChatSession::create([
            'user_id' => Auth::id(),
            'guest_name' => $request->guest_name,
            'guest_email' => $request->guest_email,
            'guest_whatsapp' => $request->guest_whatsapp,
            'category' => $request->category,
            'status' => 'open',
        ]);

        return response()->json(['session' => $session]);
    }

    public function fetchMessages($sessionId)
    {
        $session = ChatSession::findOrFail($sessionId);
        
        // Basic security: if user logged in, check if it's their session
        if (Auth::check() && $session->user_id !== Auth::id()) {
            // For guests, we rely on the session ID in local storage for simplicity in this demo
            abort(403);
        }

        $messages = $session->messages()->with(['sender:id,name', 'product.media'])->orderBy('created_at', 'asc')->get();
        return response()->json([
            'messages' => $messages,
            'status' => $session->status
        ]);
    }

    public function sendMessage(Request $request, $sessionId)
    {
        $request->validate([
            'message' => 'nullable|string',
            'product_id' => 'nullable|exists:products,id',
        ]);

        if (empty($request->message) && empty($request->product_id)) {
            return response()->json(['error' => 'Message or product is required.'], 422);
        }

        $session = ChatSession::findOrFail($sessionId);

        $senderType = Auth::check() ? 'user' : 'guest';

        $message = $session->messages()->create([
            'sender_type' => $senderType,
            'sender_id' => Auth::id(),
            'message' => $request->message ?? '',
            'product_id' => $request->product_id,
        ]);

        $message->load(['sender:id,name', 'product.media']);

        try {
            // Broadcast event
            broadcast(new NewChatMessage($message))->toOthers();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to broadcast chat message: ' . $e->getMessage());
        }

        // Send Telegram Notification
        (new AdminTelegramChatNotification($message))->sendToTelegram();

        return response()->json(['message' => $message]);
    }
}
