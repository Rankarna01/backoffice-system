<?php

namespace App\Domain\Trading\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class JournalTrade extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'journal_trades';

    protected $fillable = [
        'uuid',
        'user_id',
        'entry_time',
        'exit_time',
        'pair',
        'type',
        'status',
        'currency',
        'risk_amount',
        'profit_loss',
        'setup',
        'emotion',
        'session',
        'is_disciplined',
        'notes',
        'screenshot_url',
        'tags',
    ];

    protected $casts = [
        'entry_time' => 'datetime',
        'exit_time' => 'datetime',
        'risk_amount' => 'decimal:2',
        'profit_loss' => 'decimal:2',
        'is_disciplined' => 'boolean',
        'tags' => 'array',
    ];

    protected $appends = [
        'status_label',
        'type_label',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'open' => 'Masih Jalan',
            'win' => 'Win (Hit TP)',
            'loss' => 'Loss (Hit SL)',
            'breakeven' => 'Breakeven',
            'closed' => 'Closed',
            default => ucfirst($this->status),
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'long' => 'Long (Buy)',
            'short' => 'Short (Sell)',
            default => ucfirst($this->type),
        };
    }

    /**
     * Scope for filtering by user
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}
