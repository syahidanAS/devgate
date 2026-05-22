<?php

namespace App\Notifications;

use App\Models\Reply;
use App\Models\Thread;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class ThreadReplyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public readonly Reply $reply,
        public readonly Thread $thread
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the array representation of the notification for database storage.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $replierName = $this->reply->user ? $this->reply->user->name : 'Tamu Anonim';

        return [
            'type'            => 'thread_reply',
            'thread_id'       => $this->thread->id,
            'thread_title'    => $this->thread->title,
            'thread_slug'     => $this->thread->slug,
            'reply_id'        => $this->reply->id,
            'replier_name'    => $replierName,
            'reply_snippet'   => Str::limit(strip_tags($this->reply->body), 80),
            'url'             => route('forum.show', $this->thread->slug) . '#reply-' . $this->reply->id,
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $replierName = $this->reply->user ? $this->reply->user->name : 'Tamu Anonim';
        $threadUrl = route('forum.show', $this->thread->slug) . '#reply-' . $this->reply->id;

        return (new MailMessage)
            ->subject('Balasan Baru di Topik Diskusi Anda: ' . $this->thread->title)
            ->markdown('emails.forum.reply_received', [
                'notifiable'  => $notifiable,
                'replierName' => $replierName,
                'thread'      => $this->thread,
                'reply'       => $this->reply,
                'url'         => $threadUrl,
            ]);
    }
}
