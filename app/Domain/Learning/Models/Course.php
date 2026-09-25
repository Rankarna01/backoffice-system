<?php

namespace App\Domain\Learning\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'mentor_id',
        'category_id',
        'title',
        'slug',
        'subtitle',
        'description',
        'thumbnail_media_id',
        'preview_video_ref',
        'level',
        'language',
        'access_type',
        'price',
        'compare_at_price',
        'currency',
        'is_sequential',
        'has_certificate',
        'status',
        'duration_seconds',
        'lessons_count',
        'students_count',
        'rating_avg',
        'rating_count',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'compare_at_price' => 'decimal:2',
            'is_sequential' => 'boolean',
            'has_certificate' => 'boolean',
            'duration_seconds' => 'integer',
            'lessons_count' => 'integer',
            'students_count' => 'integer',
            'rating_avg' => 'decimal:2',
            'rating_count' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($course) {
            if (empty($course->uuid)) {
                $course->uuid = (string) Str::uuid();
            }
            if (empty($course->slug)) {
                $course->slug = Str::slug($course->title);
            }
            if (empty($course->currency)) {
                $course->currency = 'IDR';
            }
        });
    }

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function modules(): HasMany
    {
        return $this->hasMany(Module::class)->orderBy('sort_order', 'asc');
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('sort_order', 'asc');
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopeByMentor(Builder $query, int $mentorId): Builder
    {
        return $query->where('mentor_id', $mentorId);
    }
}
