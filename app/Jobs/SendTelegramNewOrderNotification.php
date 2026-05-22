<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendTelegramNewOrderNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $order;

    /**
     * Create a new job instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $botToken = config('services.telegram.bot_token');
        $chatId = config('services.telegram.admin_chat_id');

        if (empty($botToken) || empty($chatId)) {
            Log::warning('Telegram configuration missing. Skipping admin notification.');
            return;
        }

        $itemsText = "";
        foreach ($this->order->items as $item) {
            $itemsText .= "- {$item->product_name} (x{$item->quantity})\n";
        }

        $url = route('cms.orders.show', $this->order->order_number);

        $message = "🚨 *PESANAN BARU MASUK!* 🚨\n\n"
                 . "👤 *Pelanggan:* {$this->order->user->name}\n"
                 . "📧 *Email:* {$this->order->user->email}\n"
                 . "🏷️ *No. Order:* `{$this->order->order_number}`\n"
                 . "💰 *Total:* Rp " . number_format($this->order->total, 0, ',', '.') . "\n"
                 . "🚚 *Kurir:* " . strtoupper($this->order->courier) . " - {$this->order->courier_service}\n\n"
                 . "📦 *Produk:*\n"
                 . $itemsText . "\n"
                 . "🔗 [Lihat Detail di CMS]({$url})";

        try {
            $response = Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id'    => $chatId,
                'text'       => $message,
                'parse_mode' => 'Markdown',
            ]);

            if (!$response->successful()) {
                Log::error('Telegram API Error: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Telegram Exception: ' . $e->getMessage());
        }
    }
}
