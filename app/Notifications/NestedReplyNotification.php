<?php

namespace App\Notifications;

use App\Models\Reply;
use App\Models\Thread;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NestedReplyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param Reply  $reply       The new nested reply that was just posted.
     * @param Reply  $parentReply The reply that was responded to.
     * @param Thread $thread      The thread this all belongs to.
     */
    public function __construct(
        public readonly Reply  $reply,
        public readonly Reply  $parentReply,
        public readonly Thread $thread
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the array representation of the notification for database storage.
     */
    public function toDatabase(object $notifiable): array
    {
        $replierName = $this->reply->user?->name ?? 'Tamu Anonim';

        return [
            'type'               => 'nested_reply',
            'thread_id'          => $this->thread->id,
            'thread_title'       => $this->thread->title,
            'thread_slug'        => $this->thread->slug,
            'reply_id'           => $this->reply->id,
            'parent_reply_id'    => $this->parentReply->id,
            'replier_name'       => $replierName,
            'reply_snippet'      => Str::limit(strip_tags($this->reply->body), 100),
            'parent_snippet'     => Str::limit(strip_tags($this->parentReply->body), 60),
            'url'                => route('forum.show', $this->thread->slug) . '#reply-' . $this->reply->id,
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $replierName = $this->reply->user?->name ?? 'Tamu Anonim';
        $threadUrl   = route('forum.show', $this->thread->slug) . '#reply-' . $this->reply->id;

        return (new MailMessage)
            ->subject($replierName . ' membalas komentar Anda di: ' . $this->thread->title)
            ->markdown('emails.forum.nested_reply_received', [
                'notifiable'   => $notifiable,
                'replierName'  => $replierName,
                'thread'       => $this->thread,
                'reply'        => $this->reply,
                'parentReply'  => $this->parentReply,
                'url'          => $threadUrl,
            ]);
    }
}
