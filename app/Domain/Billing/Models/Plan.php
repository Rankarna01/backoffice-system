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
        });
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
