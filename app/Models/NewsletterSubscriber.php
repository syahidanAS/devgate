<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NewsletterSubscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'name',
        'token',
        'confirmed_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'confirmed_at' => 'datetime',
            'is_active'    => 'boolean',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (NewsletterSubscriber $sub) {
            if (empty($sub->token)) {
                $sub->token = Str::random(32);
            }
        });
    }
}
