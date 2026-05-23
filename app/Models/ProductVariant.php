<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'sku',
        'price',
        'stock',
        'weight',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'price'  => 'integer',
            'stock'  => 'integer',
            'weight' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Accessors
    public function getEffectivePriceAttribute(): int
    {
        return $this->price ?? $this->product->effective_price;
    }

    public function getEffectiveWeightAttribute(): int
    {
        return $this->weight ?? $this->product->weight;
    }
}
