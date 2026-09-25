<?php

namespace App\Domain\Market\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MarketNews extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'market_news';

    protected $fillable = [
        'author_id',
        'title',
        'slug',
        'summary',
        'content',
        'category',
        'impact_level',
        'sentiment',
        'source',
        'source_url',
        'cover_image_url',
        'related_symbols',
        'is_breaking',
        'is_featured',
        'status',
        'views_count',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'related_symbols' => 'array',
            'is_breaking' => 'boolean',
            'is_featured' => 'boolean',
            'views_count' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function getImpactColorAttribute(): string
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
            'high' => 'High Impact 🔴',
            'medium' => 'Medium Impact 🟡',
            'low' => 'Low Impact 🟢',
            default => ucfirst($this->impact_level),
        };
    }

    public function getSentimentColorAttribute(): string
    {
        return match ($this->sentiment) {
            'bullish' => 'success',
            'bearish' => 'danger',
            default => 'gray',
        };
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'forex' => 'Forex Currencies',
            'commodities' => 'Komoditas (Emas/Minyak)',
            'crypto' => 'Kripto Derivatif',
            'indices' => 'Indeks Saham',
            'central_banks' => 'Bank Sentral',
            'macro_economy' => 'Ekonomi Makro',
            default => ucfirst($this->category),
        };
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopeBreaking(Builder $query): Builder
    {
        return $query->where('is_breaking', true);
    }

    public function scopeHighImpact(Builder $query): Builder
    {
        return $query->where('impact_level', 'high');
    }

    public function scopeCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }
}
