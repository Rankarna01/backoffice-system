<?php

namespace App\Domain\Learning\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseEnrollment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'course_id',
        'order_id',
        'subscription_id',
        'progress_percentage',
        'completed_lessons_count',
        'enrolled_at',
        'completed_at',
        'expired_at',
    ];

    protected function casts(): array
    {
        return [
            'progress_percentage' => 'decimal:2',
            'completed_lessons_count' => 'integer',
            'enrolled_at' => 'datetime',
            'completed_at' => 'datetime',
            'expired_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
