<?php

namespace App\Domain\Learning\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bookmark extends Model
{
    use HasFactory;

    protected $table = 'bookmarks';

    protected $fillable = [
        'user_id',
        'bookmarkable_type',
        'bookmarkable_id',
        'note',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'bookmarkable_id');
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class, 'bookmarkable_id');
    }

    /**
     * Resolve the target item with enriched details
     */
    public function getDetailsAttribute(): array
    {
        if ($this->bookmarkable_type === 'course') {
            $course = Course::with(['category', 'mentor'])->find($this->bookmarkable_id);
            if (!$course) return [];

            return [
                'type' => 'course',
                'title' => $course->title,
                'slug' => $course->slug,
                'course_slug' => $course->slug,
                'thumbnail_url' => $course->thumbnail_url ?? 'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=800&auto=format&fit=crop&q=80',
                'category_name' => $course->category?->name ?? 'Trading General',
                'mentor_name' => $course->mentor?->name ?? 'Trading Mentor',
                'level' => $course->level ?? 'Semua Tingkat',
                'duration_minutes' => $course->duration_minutes ?? 120,
            ];
        }

        if ($this->bookmarkable_type === 'lesson') {
            $lesson = Lesson::with(['module.course.category', 'module.course.mentor'])->find($this->bookmarkable_id);
            if (!$lesson) return [];

            $course = $lesson->module?->course;

            return [
                'type' => 'lesson',
                'title' => $lesson->title,
                'uuid' => $lesson->uuid,
                'course_title' => $course?->title ?? 'Kursus',
                'course_slug' => $course?->slug ?? '',
                'thumbnail_url' => $course?->thumbnail_url ?? 'https://images.unsplash.com/photo-1642543492481-44e81e3914a7?w=800&auto=format&fit=crop&q=80',
                'category_name' => $course?->category?->name ?? 'Analisis Teknikal',
                'mentor_name' => $course?->mentor?->name ?? 'Trading Mentor',
                'duration_minutes' => $lesson->duration_minutes ?? 15,
                'module_title' => $lesson->module?->title ?? '',
            ];
        }

        return [];
    }
}
