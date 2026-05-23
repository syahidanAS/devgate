<?php

namespace App\Notifications;

use App\Models\ChatSessionMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ChatReplyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public ChatSessionMessage $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(ChatSessionMessage $message)
    {
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Balasan Baru dari Tim DevGate')
            ->greeting('Halo!')
            ->line('Anda mendapatkan balasan baru terkait pertanyaan Anda di layanan kami.')
            ->line('**Pesan:**')
            ->line('"' . $this->message->message . '"')
            ->action('Lanjutkan Percakapan', url('/'))
            ->line('Terima kasih telah menggunakan aplikasi kami!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
