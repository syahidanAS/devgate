<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reply extends Model
{
    protected $fillable = [
        'thread_id', 'user_id', 'parent_id', 'body', 'is_solution', 'likes',
    ];

    protected $casts = [
        'is_solution' => 'boolean',
    ];

    // ── Relationships ─────────────────────────────────────────
    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function likedByUsers(): HasMany
    {
        return $this->hasMany(ThreadLike::class);
    }

    /**
     * The parent reply this reply is responding to (nested reply).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Reply::class, 'parent_id');
    }

    /**
     * Direct children (nested) replies of this reply, loaded recursively.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Reply::class, 'parent_id')->with('user', 'children')->orderBy('created_at');
    }

    // ── Helpers ───────────────────────────────────────────────
    /**
     * Mark this reply as the solution and update the parent thread.
     */
    public function markAsSolution(): void
    {
        // Clear any previous solution on the thread
        $this->thread->replies()->update(['is_solution' => false]);

        // Mark this reply
        $this->update(['is_solution' => true]);

        // Mark the thread as solved
        $this->thread->update(['is_solved' => true]);
    }

    /**
     * Check if given user has already liked this reply.
     */
    public function isLikedBy(?User $user): bool
    {
        if (!$user) return false;
        return $this->likedByUsers()->where('user_id', $user->id)->exists();
    }
}
