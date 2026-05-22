<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use App\Notifications\ArticleCommentNotification;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification as NotificationFacade;

class CommentController extends Controller
{
    /**
     * Store a comment on an article.
     */
    public function store(Article $article, Request $request)
    {
        if (!$article->allow_comments) {
            return back()->with('error', 'Komentar dinonaktifkan untuk artikel ini.');
        }

        $rules = [
            'body'      => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id',
        ];

        // If not logged in, require guest name and email
        if (!Auth::check()) {
            $rules['guest_name'] = 'required|string|max:100';
            $rules['guest_email'] = 'required|email|max:150';
        }

        $validated = $request->validate($rules);

        $comment = new Comment();
        $comment->article_id = $article->id;
        $comment->body = clean($validated['body']); // Purify HTML input
        $comment->parent_id = $validated['parent_id'] ?? null;

        if (Auth::check()) {
            $comment->user_id = Auth::id();
            // Automatically approve authenticated comments
            $comment->status = 'approved';
        } else {
            $comment->guest_name = $validated['guest_name'];
            $comment->guest_email = $validated['guest_email'];
            // Guest comments must be approved by admin
            $comment->status = 'pending';
        }

        $comment->save();

        // Notify all superadmins and authors about the new comment
        try {
            $recipients = User::role(['superadmin', 'author'])->get();
            NotificationFacade::send($recipients, new ArticleCommentNotification($comment, $article));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to send comment notification: ' . $e->getMessage());
        }

        $message = Auth::check() 
            ? 'Komentar berhasil diposting!' 
            : 'Komentar Anda sedang menunggu moderasi admin.';

        return back()->with('success', $message);
    }
}
