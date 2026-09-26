<?php

namespace App\Domain\Market\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EconomicCalendarConfig extends Model
{
    use HasFactory;

    protected $table = 'economic_calendar_configs';

    protected $fillable = [
        'provider',
        'name',
        'api_key',
        'base_url',
        'status',
        'last_tested_at',
        'last_error_message',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_tested_at' => 'datetime',
        ];
    }

    public function getIsDemoAttribute(): bool
    {
        return empty($this->api_key) || $this->api_key === 'guest:guest';
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'connected' => 'success',
            'error' => 'danger',
            default => 'info',
        };
    }

    public function getProviderNameAttribute(): string
    {
        return $this->provider === 'fcsapi' ? 'FCS API (fcsapi.com)' : 'Trading Economics';
    }
}
