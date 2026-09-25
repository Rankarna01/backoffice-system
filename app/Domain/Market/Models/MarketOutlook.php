<?php

namespace App\Domain\Market\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MarketOutlook extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'market_outlooks';

    protected $fillable = [
        'mentor_id',
        'title',
        'slug',
        'summary',
        'content',
        'sentiment',
        'market_category',
        'featured_pairs',
        'time_horizon',
        'cover_image_url',
        'key_takeaways',
        'support_resistance_levels',
        'is_premium',
        'status',
        'views_count',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'featured_pairs' => 'array',
            'key_takeaways' => 'array',
            'support_resistance_levels' => 'array',
            'is_premium' => 'boolean',
            'views_count' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function getSentimentColorAttribute(): string
    {
        return match ($this->sentiment) {
            'bullish' => 'success',
            'bearish' => 'danger',
            'neutral' => 'warning',
            'volatile' => 'primary',
            default => 'gray',
        };
    }

    public function getSentimentLabelAttribute(): string
    {
        return match ($this->sentiment) {
            'bullish' => 'Bullish 🐂',
            'bearish' => 'Bearish 🐻',
            'neutral' => 'Neutral ⚖️',
            'volatile' => 'Volatile ⚡',
            default => ucfirst($this->sentiment),
        };
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->market_category) {
            'commodities' => 'Komoditas (Emas/Minyak)',
            'forex' => 'Forex Currencies',
            'crypto' => 'Kripto Derivatif',
            'indices' => 'Indeks Saham',
            'multi_asset' => 'Multi-Asset Macro',
            default => ucfirst($this->market_category),
        };
    }

    public function getTimeHorizonLabelAttribute(): string
    {
        return match ($this->time_horizon) {
            'weekly' => 'Mingguan (Weekly)',
            'monthly' => 'Bulanan (Monthly)',
            'quarterly' => 'Kuartalan (Q)',
            'special_report' => 'Special Report',
            default => ucfirst($this->time_horizon),
        };
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopeCategory(Builder $query, string $category): Builder
    {
        return $query->where('market_category', $category);
    }

    public function scopeSentiment(Builder $query, string $sentiment): Builder
    {
        return $query->where('sentiment', $sentiment);
    }
}
