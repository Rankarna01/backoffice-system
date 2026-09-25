<?php

namespace App\Domain\Learning\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quiz extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_id',
        'module_id',
        'title',
        'instructions',
        'passing_score',
        'max_attempts',
        'time_limit_minutes',
        'shuffle_questions',
        'shuffle_options',
        'show_answers',
        'is_required',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'passing_score' => 'integer',
            'max_attempts' => 'integer',
            'time_limit_minutes' => 'integer',
            'shuffle_questions' => 'boolean',
            'shuffle_options' => 'boolean',
            'show_answers' => 'boolean',
            'is_required' => 'boolean',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('sort_order', 'asc');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }
}
