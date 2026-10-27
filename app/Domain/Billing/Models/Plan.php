<?php

namespace App\Domain\Billing\Models;

use App\Domain\Learning\Models\Course;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Plan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'plans';

    protected $fillable = [
        'name',
        'slug',
        'tier',
        'badge',
        'description',
        'type',
        'duration_days',
        'price',
        'compare_at_price',
        'currency',
        'features',
        'includes_all_courses',
        'includes_signals',
        'ai_monthly_quota',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'duration_days' => 'integer',
            'price' => 'decimal:2',
            'compare_at_price' => 'decimal:2',
            'features' => 'array',
            'includes_all_courses' => 'boolean',
            'includes_signals' => 'boolean',
            'ai_monthly_quota' => 'integer',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($plan) {
            if (empty($plan->slug)) {
                $plan->slug = Str::slug($plan->name);
            }
            if (empty($plan->tier)) {
                $plan->tier = $plan->determineTier();
            }
        });
    }

    public function determineTier(): string
    {
        if ($this->price <= 0 || str_contains(strtolower($this->slug ?? $this->name ?? ''), 'free')) {
            return 'free';
        }
        if (str_contains(strtolower($this->slug ?? $this->name ?? ''), 'standard') || (float) $this->price < 300000) {
            return 'standard';
        }
        return 'pro';
    }

    public function getTierAttribute(): string
    {
        if (! empty($this->attributes['tier'])) {
            return $this->attributes['tier'];
        }
        return $this->determineTier();
    }

    public function getBadgeAttribute(): ?string
    {
        if (! empty($this->attributes['badge'])) {
            return $this->attributes['badge'];
        }
        return match ($this->tier) {
            'free' => 'Starter Choice',
            'standard' => 'Paling Populer',
            'pro' => 'VIP Recommended',
            default => null,
        };
    }

    public function isFree(): bool
    {
        return $this->tier === 'free' || (float) $this->price <= 0;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByTier($query, string $tier)
    {
        return $query->where('tier', $tier);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'plan_course');
    }
}
