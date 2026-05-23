<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Article extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'excerpt',
        'body',
        'thumbnail',
        'status',
        'published_at',
        'scheduled_at',
        'view_count',
        'reading_time',
        'is_featured',
        'allow_comments',
        'meta_title',
        'meta_description',
        'og_image',
        'table_of_contents',
    ];

    protected function casts(): array
    {
        return [
            'published_at'     => 'datetime',
            'scheduled_at'     => 'datetime',
            'is_featured'      => 'boolean',
            'allow_comments'   => 'boolean',
            'table_of_contents'=> 'array',
        ];
    }

    // ──────────────────────────────────────────
    // Media Library Conversions
    // ──────────────────────────────────────────

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumbnail')
            ->width(400)
            ->height(300)
            ->format('webp')
            ->performOnCollections('thumbnails');

        $this->addMediaConversion('og_image')
            ->width(1200)
            ->height(630)
            ->format('webp')
            ->performOnCollections('thumbnails');

        $this->addMediaConversion('medium')
            ->width(800)
            ->format('webp')
            ->performOnCollections('thumbnails', 'content');
    }

    // ──────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ArticleCategory::class, 'category_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(ArticleTag::class, 'article_tag', 'article_id', 'article_tag_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function approvedComments(): HasMany
    {
        return $this->hasMany(Comment::class)->where('status', 'approved')->whereNull('parent_id');
    }

    public function relatedProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'article_product')
            ->withPivot('sort_order', 'context')
            ->orderByPivot('sort_order');
    }

    // ──────────────────────────────────────────
    // Scopes
    // ──────────────────────────────────────────

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                     ->where('published_at', '<=', now());
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeTrending($query)
    {
        return $query->orderByDesc('view_count');
    }

    // ──────────────────────────────────────────
    // Accessors
    // ──────────────────────────────────────────

    public function getThumbnailUrlAttribute(): string
    {
        if ($this->thumbnail) {
            return \Illuminate\Support\Facades\Storage::disk('s3')->url($this->thumbnail);
        }

        return asset('images/article-placeholder.webp');
    }

    public function getReadingTimeTextAttribute(): string
    {
        return $this->reading_time . ' min read';
    }

    public function getIsPublishedAttribute(): bool
    {
        return $this->status === 'published' && $this->published_at?->isPast();
    }

    // ──────────────────────────────────────────
    // Boot
    // ──────────────────────────────────────────

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function (Article $article) {
            // Auto-generate reading time from body word count
            if ($article->isDirty('body')) {
                $wordCount = str_word_count(strip_tags($article->body));
                $article->reading_time = max(1, (int) ceil($wordCount / 200));
            }

            // Auto-generate slug if not set
            if (empty($article->slug)) {
                $article->slug = Str::slug($article->title);
            }
        });
    }
}
