<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Market\Models\WidgetConfig;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EconomicCalendarConfigController extends Controller
{
    /**
     * Get TradingView economic calendar widget configuration.
     *
     * Supports:
     * - GET /api/customers/{id}/economic-calendar-config
     * - GET /api/economic-calendar-config (with optional ?customer_id=...)
     */
    public function show(Request $request, ?int $id = null): JsonResponse
    {
        $customerId = $id ?: $request->query('customer_id');

        // Cast to int if provided
        $customerId = $customerId ? (int) $customerId : null;

        $config = WidgetConfig::getEffectiveConfig($customerId);

        return response()->json($config->toTradingViewConfig());
    }
}
