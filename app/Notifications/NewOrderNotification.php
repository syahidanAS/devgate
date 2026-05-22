<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Order $order,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'          => 'new_order',
            'order_id'      => $this->order->id,
            'order_number'  => $this->order->order_number,
            'buyer_name'    => $this->order->user->name ?? 'Unknown',
            'total'         => $this->order->total,
            'total_fmt'     => 'Rp ' . number_format($this->order->total, 0, ',', '.'),
            'status'        => $this->order->status,
            'url'           => route('cms.orders.show', $this->order),
        ];
    }
}
