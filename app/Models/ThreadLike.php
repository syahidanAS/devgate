<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThreadLike extends Model
{
    protected $fillable = ['reply_id', 'user_id'];

    public function reply(): BelongsTo
    {
        return $this->belongsTo(Reply::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
