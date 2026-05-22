<?php

namespace App\Notifications;

use App\Models\Article;
use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ArticleCommentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Comment $comment,
        public readonly Article $article,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $commenterName = $this->comment->user
            ? $this->comment->user->name
            : ($this->comment->guest_name ?? 'Tamu Anonim');

        return [
            'type'             => 'article_comment',
            'article_id'       => $this->article->id,
            'article_title'    => $this->article->title,
            'article_slug'     => $this->article->slug,
            'comment_id'       => $this->comment->id,
            'commenter_name'   => $commenterName,
            'comment_snippet'  => \Str::limit(strip_tags($this->comment->body), 80),
            'comment_status'   => $this->comment->status, // pending | approved
            'url'              => route('cms.articles.edit', $this->article),
        ];
    }
}
