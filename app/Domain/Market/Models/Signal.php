<?php

namespace App\Domain\Market\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Signal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'signals';

    protected $fillable = [
        'mentor_id',
        'title',
        'market_type',
        'pair',
        'timeframe',
        'action',
        'entry_price',
        'stop_loss',
        'take_profit_1',
        'take_profit_2',
        'take_profit_3',
        'risk_reward_ratio',
        'status',
        'result_pips',
        'risk_percentage',
        'analysis_notes',
        'chart_image_url',
        'is_premium',
        'published_at',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'entry_price' => 'float',
            'stop_loss' => 'float',
            'take_profit_1' => 'float',
            'take_profit_2' => 'float',
            'take_profit_3' => 'float',
            'result_pips' => 'float',
            'risk_percentage' => 'float',
            'is_premium' => 'boolean',
            'published_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function getActionColorAttribute(): string
    {
        return str_starts_with($this->action, 'BUY') ? 'success' : 'danger';
    }

    public function getMarketTypeLabelAttribute(): string
    {
        return match ($this->market_type) {
            'forex' => 'Forex Currencies',
            'commodities' => 'Komoditas (Emas / Logam)',
            'crypto' => 'Kripto Derivatif',
            'indices' => 'Indeks Saham Global',
            default => ucfirst($this->market_type),
        };
    }

    public function getMarketTypeColorAttribute(): string
    {
        return match ($this->market_type) {
            'forex' => 'info',
            'commodities' => 'warning',
            'crypto' => 'primary',
            'indices' => 'success',
            default => 'gray',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'active' => 'primary',
            'pending' => 'gray',
            'hit_tp1', 'hit_tp2', 'hit_tp3' => 'success',
            'hit_sl' => 'danger',
            'closed' => 'info',
            'cancelled' => 'gray',
            default => 'gray',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active' => 'Active Running',
            'pending' => 'Pending Order',
            'hit_tp1' => 'Hit TP 1 🎯',
            'hit_tp2' => 'Hit TP 2 🎯🎯',
            'hit_tp3' => 'Hit TP 3 🚀',
            'hit_sl' => 'Hit SL ❌',
            'closed' => 'Closed (BEP / Manual)',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($this->status),
        };
    }

    public static function computeRiskReward(string $action, float $entry, float $sl, float $tp): ?string
    {
        if ($entry <= 0 || $sl <= 0 || $tp <= 0) {
            return null;
        }

        $isBuy = str_starts_with(strtoupper($action), 'BUY');
        $risk = $isBuy ? ($entry - $sl) : ($sl - $entry);
        $reward = $isBuy ? ($tp - $entry) : ($entry - $tp);

        if ($risk <= 0 || $reward <= 0) {
            return null;
        }

        $ratio = round($reward / $risk, 1);
        return "1:{$ratio}";
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', ['active', 'pending']);
    }

    public function scopeClosed(Builder $query): Builder
    {
        return $query->whereIn('status', ['hit_tp1', 'hit_tp2', 'hit_tp3', 'hit_sl', 'closed']);
    }

    public function scopeMarket(Builder $query, string $type): Builder
    {
        return $query->where('market_type', $type);
    }
}
