<?php

namespace App\Domain\Market\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WidgetConfig extends Model
{
    use HasFactory;

    protected $table = 'widget_configs';

    protected $fillable = [
        'customer_id',
        'widget_type',
        'width',
        'height',
        'color_theme',
        'is_transparent',
        'locale',
        'importance_filter',
        'currencies',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_transparent' => 'boolean',
            'is_active' => 'boolean',
            'currencies' => 'array',
        ];
    }

    /**
     * Relationship to the user / customer if customer-specific override.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Scope query to global default config (customer_id is null).
     */
    public function scopeGlobal($query)
    {
        return $query->whereNull('customer_id');
    }

    /**
     * Retrieve the effective active config for a customer (or global default).
     */
    public static function getEffectiveConfig(?int $customerId = null, string $widgetType = 'economic_calendar'): self
    {
        // 1. Try finding customer-specific override if customerId is provided
        if ($customerId) {
            $customerConfig = static::query()
                ->where('widget_type', $widgetType)
                ->where('customer_id', $customerId)
                ->where('is_active', true)
                ->first();

            if ($customerConfig) {
                return $customerConfig;
            }
        }

        // 2. Try global default config
        $globalConfig = static::query()
            ->where('widget_type', $widgetType)
            ->whereNull('customer_id')
            ->where('is_active', true)
            ->first();

        if ($globalConfig) {
            return $globalConfig;
        }

        // 3. Fallback to fresh default instance with standard TradingView parameters
        return new static([
            'widget_type' => $widgetType,
            'customer_id' => null,
            'width' => '100%',
            'height' => '650',
            'color_theme' => 'dark',
            'is_transparent' => false,
            'locale' => 'en',
            'importance_filter' => '-1,0,1',
            'currencies' => ['USD', 'EUR', 'GBP', 'JPY', 'AUD', 'CAD', 'CHF'],
            'is_active' => true,
        ]);
    }

    /**
     * Format configuration as TradingView official embed widget parameters.
     */
    public function toTradingViewConfig(): array
    {
        $currencyList = is_array($this->currencies)
            ? implode(',', $this->currencies)
            : ($this->currencies ?: 'USD,EUR,GBP,JPY,AUD,CAD,CHF');

        return [
            // TradingView Widget camelCase properties
            'colorTheme' => $this->color_theme ?: 'dark',
            'isTransparent' => (bool) $this->is_transparent,
            'width' => $this->width ?: '100%',
            'height' => $this->height ?: '650',
            'locale' => $this->locale ?: 'en',
            'importanceFilter' => $this->importance_filter ?: '-1,0,1',
            'currencyFilter' => $currencyList,

            // Also expose snake_case properties for frontend flexibility
            'customer_id' => $this->customer_id,
            'color_theme' => $this->color_theme ?: 'dark',
            'is_transparent' => (bool) $this->is_transparent,
            'importance_filter' => $this->importance_filter ?: '-1,0,1',
            'currencies' => $currencyList,
            'is_active' => (bool) $this->is_active,
        ];
    }
}
