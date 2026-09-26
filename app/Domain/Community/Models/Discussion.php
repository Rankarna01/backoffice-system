<?php

namespace App\Domain\Community\Models;

use App\Domain\Community\Enums\DiscussionCategory;
use App\Domain\Community\Enums\DiscussionStatus;
use App\Domain\Community\Enums\ModerationStatus;
use App\Domain\Learning\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Discussion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'discussions';

    protected $fillable = [
        'user_id',
        'course_id',
        'title',
        'slug',
        'category',
        'content',
        'status',
        'moderation_status',
        'is_pinned',
        'is_locked',
        'views_count',
        'likes_count',
        'replies_count',
        'reports_count',
        'last_activity_at',
    ];

    protected function casts(): array
    {
        return [
            'category' => DiscussionCategory::class,
            'status' => DiscussionStatus::class,
            'moderation_status' => ModerationStatus::class,
            'is_pinned' => 'boolean',
            'is_locked' => 'boolean',
            'views_count' => 'integer',
            'likes_count' => 'integer',
            'replies_count' => 'integer',
            'reports_count' => 'integer',
            'last_activity_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($discussion) {
            if (empty($discussion->slug)) {
                $discussion->slug = Str::slug($discussion->title) . '-' . Str::random(6);
            }
            if (empty($discussion->last_activity_at)) {
                $discussion->last_activity_at = now();
            }
        });
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(DiscussionReply::class, 'discussion_id');
    }

    public function rootReplies(): HasMany
    {
        return $this->hasMany(DiscussionReply::class, 'discussion_id')->whereNull('parent_id');
    }

    public function scopeApproved($query)
    {
        return $query->where('moderation_status', ModerationStatus::Approved->value);
    }

    public function scopeNeedsModeration($query)
    {
        return $query->whereIn('moderation_status', [
            ModerationStatus::PendingReview->value,
            ModerationStatus::Flagged->value,
        ]);
    }

    public function scopePinnedFirst($query)
    {
        return $query->orderByDesc('is_pinned')->orderByDesc('last_activity_at');
    }
}
