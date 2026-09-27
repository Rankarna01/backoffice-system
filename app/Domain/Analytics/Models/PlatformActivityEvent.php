<?php

namespace App\Domain\Analytics\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlatformActivityEvent extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'platform_activity_events';

    protected $fillable = [
        'user_id',
        'event_name',
        'entity_type',
        'entity_id',
        'payload',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function track(string $eventName, ?int $userId = null, ?string $entityType = null, ?int $entityId = null, array $payload = []): static
    {
        return static::create([
            'user_id' => $userId,
            'event_name' => $eventName,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'payload' => $payload,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }
}
