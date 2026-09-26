<?php

namespace App\Domain\Community\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiveSessionRegistration extends Model
{
    use HasFactory;

    protected $table = 'live_session_registrations';

    protected $fillable = [
        'live_session_id',
        'user_id',
        'registered_at',
        'attended',
        'attended_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'registered_at' => 'datetime',
            'attended' => 'boolean',
            'attended_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::created(function (LiveSessionRegistration $registration) {
            $registration->liveSession?->recalculateCounts();
        });

        static::updated(function (LiveSessionRegistration $registration) {
            if ($registration->wasChanged('attended')) {
                $registration->liveSession?->recalculateCounts();
            }
        });

        static::deleted(function (LiveSessionRegistration $registration) {
            $registration->liveSession?->recalculateCounts();
        });
    }

    public function liveSession(): BelongsTo
    {
        return $this->belongsTo(LiveSession::class, 'live_session_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
