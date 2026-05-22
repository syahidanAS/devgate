<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Video extends Model
{
    protected $fillable = [
        'platform',
        'video_id',
        'thumbnail_url',
        'title',
        'description',
        'duration',
        'views',
        'sort_order',
        'is_active',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active'   => 'boolean',
            'sort_order'  => 'integer',
        ];
    }

    // ──────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ──────────────────────────────────────────
    // Scopes
    // ──────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeYoutube(Builder $query): Builder
    {
        return $query->where('platform', 'youtube');
    }

    public function scopeTiktok(Builder $query): Builder
    {
        return $query->where('platform', 'tiktok');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('created_at', 'desc');
    }

    // ──────────────────────────────────────────
    // Accessors
    // ──────────────────────────────────────────

    public function getThumbnailUrlAttribute(): string
    {
        // Use stored thumbnail URL if available (e.g. from TikTok oEmbed or manual override)
        if (!empty($this->attributes['thumbnail_url'])) {
            return $this->attributes['thumbnail_url'];
        }

        // YouTube fallback — auto-built from video_id
        if ($this->platform === 'youtube') {
            return "https://i.ytimg.com/vi/{$this->video_id}/hqdefault.jpg";
        }

        // TikTok without stored thumbnail
        return '';
    }

    public function getWatchUrlAttribute(): string
    {
        if ($this->platform === 'youtube') {
            return "https://www.youtube.com/watch?v={$this->video_id}";
        }

        // For TikTok: if video_id is a full URL, use it directly
        if (str_starts_with($this->video_id, 'http')) {
            return $this->video_id;
        }

        // Fallback: try to build vm.tiktok.com short link
        return "https://vm.tiktok.com/{$this->video_id}/";
    }

    public function getPlatformLabelAttribute(): string
    {
        return match($this->platform) {
            'youtube' => 'YouTube',
            'tiktok'  => 'TikTok',
            default   => ucfirst($this->platform),
        };
    }
}
