<?php

namespace App\Domain\Learning\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Lesson extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'course_id',
        'module_id',
        'title',
        'slug',
        'type',
        'content',
        'video_provider',
        'video_ref',
        'duration_seconds',
        'is_preview',
        'is_required',
        'sort_order',
        'status',
    ];

    protected $appends = [
        'duration_formatted',
    ];

    protected function casts(): array
    {
        return [
            'duration_seconds' => 'integer',
            'is_preview' => 'boolean',
            'is_required' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Lesson $lesson) {
            if (empty($lesson->uuid)) {
                $lesson->uuid = (string) Str::uuid();
            }

            if (empty($lesson->slug)) {
                $lesson->slug = Str::slug($lesson->title);
            }

            if (empty($lesson->course_id) && $lesson->module_id) {
                $module = Module::find($lesson->module_id);
                if ($module) {
                    $lesson->course_id = $module->course_id;
                }
            }

            if (! $lesson->sort_order && $lesson->module_id) {
                $maxOrder = static::where('module_id', $lesson->module_id)->max('sort_order') ?? 0;
                $lesson->sort_order = $maxOrder + 1;
            }
        });
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopePreview(Builder $query): Builder
    {
        return $query->where('is_preview', true);
    }

    public function getDurationFormattedAttribute(): string
    {
        if (! $this->duration_seconds) {
            return '-';
        }

        $minutes = floor($this->duration_seconds / 60);
        $seconds = $this->duration_seconds % 60;

        return sprintf('%02d:%02d', $minutes, $seconds);
    }
}
