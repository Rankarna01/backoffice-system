<?php

namespace App\Domain\Notifications\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class BroadcastNotification extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'broadcast_notifications';

    protected $fillable = [
        'uuid',
        'title',
        'body',
        'type',
        'target_audience',
        'action_url',
        'channels',
        'status',
        'scheduled_at',
        'sent_at',
        'total_recipients',
        'read_count',
        'created_by',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'channels' => 'array',
            'meta' => 'array',
            'scheduled_at' => 'datetime',
            'sent_at' => 'datetime',
            'total_recipients' => 'integer',
            'read_count' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(NotificationLog::class, 'broadcast_id');
    }

    public function getReadRateAttribute(): float
    {
        if ($this->total_recipients <= 0) {
            return 0.0;
        }

        return round(($this->read_count / $this->total_recipients) * 100, 1);
    }
}
