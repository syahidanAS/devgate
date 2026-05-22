<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'shipping_address',
        'courier',
        'courier_service',
        'subtotal',
        'shipping_cost',
        'discount',
        'total',
        'voucher_code',
        'notes',
        'tracking_number',
        'paid_at',
        'shipped_at',
        'delivered_at',
    ];

    protected function casts(): array
    {
        return [
            'shipping_address' => 'array',
            'subtotal'         => 'integer',
            'shipping_cost'    => 'integer',
            'discount'         => 'integer',
            'total'            => 'integer',
            'paid_at'          => 'datetime',
            'shipped_at'       => 'datetime',
            'delivered_at'     => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function refundRequest(): HasOne
    {
        return $this->hasOne(RefundRequest::class);
    }

    public function chatMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    public function getFormattedShippingCostAttribute(): string
    {
        return 'Rp ' . number_format($this->shipping_cost, 0, ',', '.');
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Konfirmasi',
            'awaiting_payment' => 'Menunggu Pembayaran',
            'paid' => 'Sudah Dibayar',
            'processing' => 'Sedang Diproses',
            'shipped' => 'Dalam Pengiriman',
            'delivered' => 'Sampai Tujuan',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            'refunding' => 'Pengajuan Pengembalian Dana',
            'refunded' => 'Dikembalikan (Refund)',
            default => 'Unknown',
        };
    }
}
