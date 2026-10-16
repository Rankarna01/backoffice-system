<?php

namespace App\Domain\Trading\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiveChartConfig extends Model
{
    use HasFactory;

    protected $table = 'live_chart_configs';

    protected $fillable = [
        'customer_id',
        'default_symbol',
        'default_interval',
        'theme',
        'locale',
        'timezone',
        'watchlist',
        'allow_trading',
        'default_balance',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'watchlist' => 'array',
            'allow_trading' => 'boolean',
            'is_active' => 'boolean',
            'default_balance' => 'decimal:2',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Get default watchlist pairs (Crypto, Forex & Emas) as shown in the screenshot.
     */
    public static function getDefaultWatchlist(): array
    {
        return [
            'crypto' => [
                ['symbol' => 'BINANCE:BTCUSDT', 'pair' => 'BTC/USDT', 'name' => 'Bitcoin', 'price' => 81997.98, 'change' => '+0.13%'],
                ['symbol' => 'BINANCE:ETHUSDT', 'pair' => 'ETH/USDT', 'name' => 'Ethereum', 'price' => 2461.37, 'change' => '+2.63%'],
                ['symbol' => 'BINANCE:SOLUSDT', 'pair' => 'SOL/USDT', 'name' => 'Solana', 'price' => 105.78, 'change' => '+0.86%'],
                ['symbol' => 'BINANCE:BNBUSDT', 'pair' => 'BNB/USDT', 'name' => 'BNB', 'price' => 728.17, 'change' => '-0.27%'],
                ['symbol' => 'BINANCE:XRPUSDT', 'pair' => 'XRP/USDT', 'name' => 'XRP', 'price' => 1.3299, 'change' => '+0.37%'],
                ['symbol' => 'BINANCE:DOGEUSDT', 'pair' => 'DOGE/USDT', 'name' => 'Dogecoin', 'price' => 0.08152, 'change' => '+1.96%'],
            ],
            'forex_gold' => [
                ['symbol' => 'OANDA:XAUUSD', 'pair' => 'XAU/USD', 'name' => 'Emas spot', 'price' => 4187.01, 'change' => '+0.05%'],
                ['symbol' => 'OANDA:XAGUSD', 'pair' => 'XAG/USD', 'name' => 'Perak spot', 'price' => 31.84, 'change' => '+0.42%'],
                ['symbol' => 'FX:EURUSD', 'pair' => 'EUR/USD', 'name' => 'Euro', 'price' => 1.0845, 'change' => '-0.12%'],
            ],
        ];
    }

    /**
     * Retrieve the effective active config for a customer or fallback to global.
     */
    public static function getEffectiveConfig(?int $customerId = null): self
    {
        if ($customerId) {
            $customerConfig = static::where('customer_id', $customerId)
                ->where('is_active', true)
                ->first();

            if ($customerConfig) {
                return $customerConfig;
            }
        }

        $globalConfig = static::whereNull('customer_id')
            ->where('is_active', true)
            ->first();

        if ($globalConfig) {
            return $globalConfig;
        }

        return new static([
            'customer_id' => null,
            'default_symbol' => 'OANDA:XAUUSD',
            'default_interval' => '60',
            'theme' => 'dark',
            'locale' => 'id',
            'timezone' => 'Asia/Jakarta',
            'watchlist' => static::getDefaultWatchlist(),
            'allow_trading' => true,
            'default_balance' => 10000.00,
            'is_active' => true,
        ]);
    }
}
