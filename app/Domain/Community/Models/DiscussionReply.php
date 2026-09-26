<?php

namespace App\Domain\Community\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DiscussionReply extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'discussion_replies';

    protected $fillable = [
        'discussion_id',
        'user_id',
        'parent_id',
        'content',
        'likes_count',
        'is_solution',
        'is_hidden',
    ];

    protected function casts(): array
    {
        return [
            'likes_count' => 'integer',
            'is_solution' => 'boolean',
            'is_hidden' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::created(function ($reply) {
            $reply->discussion()->increment('replies_count');
            $reply->discussion()->update(['last_activity_at' => now()]);
        });

        static::deleted(function ($reply) {
            $reply->discussion()->decrement('replies_count');
        });
    }

    public function discussion(): BelongsTo
    {
        return $this->belongsTo(Discussion::class, 'discussion_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
