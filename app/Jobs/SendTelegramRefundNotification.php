<?php

namespace App\Jobs;

use App\Models\RefundRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendTelegramRefundNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $refundRequest;

    /**
     * Create a new job instance.
     */
    public function __construct(RefundRequest $refundRequest)
    {
        $this->refundRequest = $refundRequest;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $botToken = config('services.telegram.bot_token');
        $chatId = config('services.telegram.admin_chat_id');

        if (empty($botToken) || empty($chatId)) {
            Log::warning('Telegram configuration missing. Skipping refund notification.');
            return;
        }

        $order = $this->refundRequest->order;
        if (!$order) {
            return;
        }

        $url = route('cms.orders.show', $order->order_number);
        
        $methodText = $this->refundRequest->refund_method === 'midtrans_api' 
            ? "Otomatis via Midtrans (QRIS)" 
            : "Transfer Bank Manual\n🏛️ *Bank:* " . strtoupper($this->refundRequest->bank_name) . "\n💳 *No. Rek:* `{$this->refundRequest->account_number}`\n👤 *A/N:* {$this->refundRequest->account_holder}";

        $message = "⚠️ *PENGAJUAN REFUND / PEMBATALAN BARU* ⚠️\n\n"
                 . "👤 *Pelanggan:* {$order->user->name}\n"
                 . "🏷️ *No. Order:* `{$order->order_number}`\n"
                 . "💰 *Total Refund:* Rp " . number_format($order->total, 0, ',', '.') . "\n"
                 . "📝 *Alasan:* {$this->refundRequest->reason}\n"
                 . "⚙️ *Metode:* {$methodText}\n\n"
                 . "🔗 [Tinjau Pengajuan Refund di CMS]({$url})";

        try {
            $response = Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id'    => $chatId,
                'text'       => $message,
                'parse_mode' => 'Markdown',
            ]);

            if (!$response->successful()) {
                Log::error('Telegram API Error (Refund): ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Telegram Exception (Refund): ' . $e->getMessage());
        }
    }
}
