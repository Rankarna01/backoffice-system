<?php

namespace App\Domain\Learning\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'quiz_id',
        'score',
        'is_passed',
        'attempt_number',
        'answers_payload',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
            'is_passed' => 'boolean',
            'attempt_number' => 'integer',
            'answers_payload' => 'array',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }
}
