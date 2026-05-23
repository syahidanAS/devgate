<?php

namespace App\Notifications;

use App\Models\ChatSessionMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdminTelegramChatNotification extends Notification implements ShouldQueue
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
        return [];
    }

    /**
     * Send notification to Telegram
     */
    public function sendToTelegram()
    {
        $botToken = config('services.telegram.bot_token', env('TELEGRAM_BOT_TOKEN'));
        $chatId = config('services.telegram.chat_id', env('TELEGRAM_ADMIN_CHAT_ID'));

        if (!$botToken || !$chatId) {
            return;
        }

        $session = $this->message->session;
        $senderName = $this->message->sender_type === 'user' ? $this->message->sender->name : $session->guest_name;
        $senderContact = $this->message->sender_type === 'guest' ? " (Email: {$session->guest_email}, WA: {$session->guest_whatsapp})" : "";

        $text = "💬 *Pesan Chat Baru*\n\n"
              . "*Kategori:* " . strtoupper($session->category) . "\n"
              . "*Dari:* {$senderName}{$senderContact}\n\n"
              . "*Pesan:*\n_{$this->message->message}_\n\n"
              . "Tolong segera balas via Admin Dashboard.";

        try {
            Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'Markdown',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send Telegram notification: ' . $e->getMessage());
        }
    }
}
