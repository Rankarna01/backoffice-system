<?php

namespace App\Domain\Community\Models;

use App\Domain\Community\Enums\LiveSessionPlatform;
use App\Domain\Community\Enums\LiveSessionStatus;
use App\Domain\Community\Enums\LiveSessionTier;
use App\Domain\Identity\Models\MentorProfile;
use App\Domain\Learning\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class LiveSession extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'live_sessions';

    protected $fillable = [
        'mentor_id',
        'course_id',
        'title',
        'slug',
        'description',
        'cover_image_url',
        'scheduled_at',
        'duration_minutes',
        'platform',
        'join_url',
        'passcode',
        'max_participants',
        'registered_count',
        'attended_count',
        'status',
        'recording_url',
        'recording_duration_minutes',
        'is_featured',
        'target_tier',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'duration_minutes' => 'integer',
            'platform' => LiveSessionPlatform::class,
            'max_participants' => 'integer',
            'registered_count' => 'integer',
            'attended_count' => 'integer',
            'status' => LiveSessionStatus::class,
            'recording_duration_minutes' => 'integer',
            'is_featured' => 'boolean',
            'target_tier' => LiveSessionTier::class,
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($session) {
            if (empty($session->slug)) {
                $session->slug = Str::slug($session->title) . '-' . Str::random(6);
            }
        });
    }

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(MentorProfile::class, 'mentor_id');
    }

    public function mentorProfile(): BelongsTo
    {
        return $this->belongsTo(MentorProfile::class, 'mentor_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(LiveSessionRegistration::class, 'live_session_id');
    }

    public function registeredUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'live_session_registrations', 'live_session_id', 'user_id')
            ->withPivot(['attended', 'attended_at', 'notes'])
            ->withTimestamps();
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('status', LiveSessionStatus::Upcoming);
    }

    public function scopeLive(Builder $query): Builder
    {
        return $query->where('status', LiveSessionStatus::Live);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', LiveSessionStatus::Completed);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function isLiveNow(): bool
    {
        return $this->status === LiveSessionStatus::Live;
    }

    public function canJoinNow(): bool
    {
        if ($this->status === LiveSessionStatus::Live) {
            return true;
        }

        if ($this->status === LiveSessionStatus::Upcoming && $this->scheduled_at) {
            $windowStart = $this->scheduled_at->copy()->subMinutes(15);
            $windowEnd = $this->scheduled_at->copy()->addMinutes($this->duration_minutes);
            return now()->between($windowStart, $windowEnd);
        }

        return false;
    }

    public function isFull(): bool
    {
        return $this->max_participants > 0 && $this->registered_count >= $this->max_participants;
    }

    public function recalculateCounts(): void
    {
        $this->updateQuietly([
            'registered_count' => $this->registrations()->count(),
            'attended_count' => $this->registrations()->where('attended', true)->count(),
        ]);
    }
}
