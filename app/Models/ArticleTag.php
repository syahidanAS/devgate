<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class ArticleTag extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug'];

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'article_tag', 'article_tag_id', 'article_id');
    }

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function (ArticleTag $tag) {
            if (empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });
    }
}
