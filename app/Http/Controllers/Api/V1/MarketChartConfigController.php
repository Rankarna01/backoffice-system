<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Trading\Models\LiveChartConfig;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MarketChartConfigController extends Controller
{
    /**
     * Retrieve active Live Chart & TradingView configuration and symbol watchlists.
     */
    public function show(Request $request): JsonResponse
    {
        $customerId = $request->user()?->id ?? $request->query('customer_id');
        $config = LiveChartConfig::getEffectiveConfig($customerId ? (int) $customerId : null);

        return response()->json([
            'success' => true,
            'message' => 'Konfigurasi Live Chart TradingView berhasil dimuat.',
            'data' => [
                'default_symbol' => $config->default_symbol ?: 'OANDA:XAUUSD',
                'default_interval' => $config->default_interval ?: '60',
                'theme' => $config->theme ?: 'dark',
                'locale' => $config->locale ?: 'id',
                'timezone' => $config->timezone ?: 'Asia/Jakarta',
                'watchlist' => $config->watchlist ?: LiveChartConfig::getDefaultWatchlist(),
                'allow_trading' => (bool) $config->allow_trading,
                'default_balance' => (float) $config->default_balance,
            ],
        ]);
    }
}
