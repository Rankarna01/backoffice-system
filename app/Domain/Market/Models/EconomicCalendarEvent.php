<?php

namespace App\Domain\Market\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EconomicCalendarEvent extends Model
{
    use HasFactory;

    protected $table = 'economic_calendar_events';

    protected $fillable = [
        'country',
        'currency',
        'event_name',
        'impact_level',
        'actual',
        'forecast',
        'previous',
        'unit',
        'event_date',
        'period',
        'source',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'datetime',
        ];
    }

    public function getImpactBadgeColorAttribute(): string
    {
        return match ($this->impact_level) {
            'high' => 'danger',
            'medium' => 'warning',
            'low' => 'success',
            default => 'gray',
        };
    }

    public function getImpactLabelAttribute(): string
    {
        return match ($this->impact_level) {
            'high' => 'High 🔴',
            'medium' => 'Medium 🟡',
            'low' => 'Low 🟢',
            default => ucfirst($this->impact_level),
        };
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('event_date', today());
    }

    public function scopeThisWeek(Builder $query): Builder
    {
        return $query->whereBetween('event_date', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeHighImpact(Builder $query): Builder
    {
        return $query->where('impact_level', 'high');
    }

    public function scopeCurrency(Builder $query, string $currency): Builder
    {
        return $query->where('currency', $currency);
    }
}
