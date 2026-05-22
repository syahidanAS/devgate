<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Thread extends Model
{
    protected $fillable = [
        'user_id', 'title', 'slug', 'body', 'tags', 'views', 'is_solved', 'is_closed',
    ];

    protected $casts = [
        'tags'      => 'array',
        'is_solved' => 'boolean',
        'is_closed' => 'boolean',
    ];

    // ── Relationships ─────────────────────────────────────────
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Reply::class)->orderBy('is_solution', 'desc')->orderBy('created_at');
    }

    public function solutionReply(): HasOne
    {
        return $this->hasOne(Reply::class)->where('is_solution', true);
    }

    // ── Accessors ─────────────────────────────────────────────
    public function getExcerptAttribute(): string
    {
        return Str::limit(strip_tags($this->body), 160);
    }

    // ── Scopes ────────────────────────────────────────────────
    public function scopeSolved($query)
    {
        return $query->where('is_solved', true);
    }

    public function scopeUnsolved($query)
    {
        return $query->where('is_solved', false);
    }

    // ── Helpers ───────────────────────────────────────────────
    /**
     * Auto-generate a unique slug from the given title.
     */
    public static function generateSlug(string $title): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i    = 2;
        while (static::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }

    public function incrementViews(): void
    {
        $this->increment('views');
    }
}
