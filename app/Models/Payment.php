<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'midtrans_order_id',
        'midtrans_transaction_id',
        'payment_type',
        'payment_method',
        'status',
        'amount',
        'va_number',
        'qris_url',
        'payload',
        'snap_token',
        'paid_at',
        'expired_at',
        'reminder_email_sent_at',
        'expired_email_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'amount'                 => 'integer',
            'payload'                => 'array',
            'paid_at'                => 'datetime',
            'expired_at'             => 'datetime',
            'reminder_email_sent_at' => 'datetime',
            'expired_email_sent_at'  => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }
}
