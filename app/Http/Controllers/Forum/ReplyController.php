<?php

namespace App\Http\Controllers\Forum;

use App\Http\Controllers\Controller;
use App\Models\Reply;
use App\Models\Thread;
use App\Models\ThreadLike;
use App\Notifications\NestedReplyNotification;
use App\Notifications\ThreadReplyNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ReplyController extends Controller
{
    /**
     * Store a new reply to a thread (or a nested reply to another reply).
     */
    public function store(Request $request, Thread $thread)
    {
        abort_if($thread->is_closed, 403, 'Diskusi ini telah ditutup.');

        $request->validate([
            'body'            => ['required', 'string', 'min:10'],
            'parent_reply_id' => ['nullable', 'integer', 'exists:replies,id'],
        ]);

        $parentReply = null;
        if ($request->filled('parent_reply_id')) {
            $parentReply = Reply::find($request->parent_reply_id);

            // Security: ensure the parent reply belongs to this thread
            if ($parentReply && $parentReply->thread_id !== $thread->id) {
                $parentReply = null;
            }
        }

        $reply = Reply::create([
            'thread_id' => $thread->id,
            'user_id'   => Auth::id(),
            'parent_id' => $parentReply?->id,
            'body'      => $request->body,
        ]);

        // ── Notifications ──────────────────────────────────────────────────────

        // 1. Notify the PARENT REPLY author (nested reply notification)
        if ($parentReply && $parentReply->user_id !== Auth::id()) {
            try {
                $parentReply->user->notify(new NestedReplyNotification($reply, $parentReply, $thread));
            } catch (\Exception $e) {
                Log::error('Gagal mengirim notifikasi nested reply: ' . $e->getMessage());
            }
        }

        // 2. Notify the THREAD OWNER (only if they are not the replier and not already notified above)
        $notifiedUserIds = collect([$parentReply?->user_id]);
        if ($thread->user_id !== Auth::id() && !$notifiedUserIds->contains($thread->user_id)) {
            try {
                $thread->user->notify(new ThreadReplyNotification($reply, $thread));
            } catch (\Exception $e) {
                Log::error('Gagal mengirim notifikasi balasan thread: ' . $e->getMessage());
            }
        }

        return redirect()->route('forum.show', $thread->slug . '#replies')
            ->with('success', 'Balasan berhasil dikirim!');
    }

    /**
     * Mark a reply as the solution (thread owner only).
     */
    public function markSolved(Thread $thread, Reply $reply)
    {
        abort_unless(Auth::id() === $thread->user_id, 403, 'Hanya pemilik topik yang bisa menandai jawaban terbaik.');
        abort_unless($reply->thread_id === $thread->id, 404);

        $reply->markAsSolution();

        return back()->with('success', 'Jawaban terbaik berhasil ditandai!');
    }

    /**
     * Toggle like on a reply.
     */
    public function like(Reply $reply)
    {
        $userId = Auth::id();
        $existing = ThreadLike::where('reply_id', $reply->id)
            ->where('user_id', $userId)
            ->first();

        if ($existing) {
            // Unlike
            $existing->delete();
            $reply->decrement('likes');
            $liked = false;
        } else {
            // Like
            ThreadLike::create(['reply_id' => $reply->id, 'user_id' => $userId]);
            $reply->increment('likes');
            $liked = true;
        }

        return response()->json([
            'liked'  => $liked,
            'count'  => $reply->fresh()->likes,
        ]);
    }

    /**
     * Delete a reply (owner or superadmin).
     */
    public function destroy(Reply $reply)
    {
        $user = Auth::user();
        abort_unless($user->id === $reply->user_id || $user->hasRole('superadmin'), 403);

        $thread = $reply->thread;
        $reply->delete();

        return redirect()->route('forum.show', $thread->slug)
            ->with('success', 'Balasan dihapus.');
    }
}
