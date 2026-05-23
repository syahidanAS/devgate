<?php

namespace App\Http\Controllers\CMS;

use App\Events\NewChatMessage;
use App\Http\Controllers\Controller;
use App\Models\ChatSession;
use App\Models\ChatSessionMessage;
use App\Notifications\ChatReplyNotification;
use App\Models\Product;
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
        $messages = $session->messages()->with(['sender:id,name', 'product.media'])->orderBy('created_at', 'asc')->get();
        return response()->json([
            'messages' => $messages,
            'status' => $session->status
        ]);
    }

    public function searchProducts(Request $request)
    {
        $query = $request->get('q', '');
        $products = Product::active()
            ->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($query) . '%'])
            ->with('media')
            ->limit(10)
            ->get();
            
        return response()->json(['products' => $products]);
    }

    public function reply(Request $request, $sessionId)
    {
        $request->validate([
            'message' => 'nullable|string',
            'product_id' => 'nullable|exists:products,id',
        ]);

        if (empty($request->message) && empty($request->product_id)) {
            return response()->json(['error' => 'Message or product is required.'], 422);
        }

        $session = $this->getAuthorizedSession($sessionId);

        $message = $session->messages()->create([
            'sender_type' => 'admin',
            'sender_id' => Auth::id(),
            'message' => $request->message ?? '',
            'product_id' => $request->product_id,
        ]);

        $message->load(['sender:id,name', 'product.media']);

        // Broadcast to customer
        broadcast(new NewChatMessage($message))->toOthers();

        // Send Email Notification
        $email = $session->user_id ? $session->user->email : $session->guest_email;
        if ($email) {
            Notification::route('mail', $email)->notify(new ChatReplyNotification($message));
        }

        // Update session updated_at to bring it to top
        $session->touch();

        return response()->json(['message' => $message]);
    }

    public function close($sessionId)
    {
        $session = $this->getAuthorizedSession($sessionId);
        
        if ($session->status === 'closed') {
            return response()->json(['message' => 'Session already closed.'], 400);
        }

        $session->update(['status' => 'closed']);

        // Create a system message
        $message = $session->messages()->create([
            'sender_type' => 'system',
            'message' => 'Sesi obrolan ini telah diakhiri oleh Admin.',
        ]);

        broadcast(new NewChatMessage($message))->toOthers();

        return response()->json(['success' => true, 'message' => $message]);
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
