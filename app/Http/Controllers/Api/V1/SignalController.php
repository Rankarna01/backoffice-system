<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Market\Models\Signal;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SignalController extends Controller
{
    /**
     * Display a listing of trading signals.
     * GET /api/v1/signals
     */
    public function index(Request $request): JsonResponse
    {
        $query = Signal::query()->with(['mentor:id,name,email']);

        // Filter by market type
        if ($request->filled('market_type') && $request->market_type !== 'all') {
            $query->where('market_type', $request->market_type);
        }

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            if ($request->status === 'running') {
                $query->whereIn('status', ['active', 'pending']);
            } elseif ($request->status === 'resolved') {
                $query->whereIn('status', ['hit_tp1', 'hit_tp2', 'hit_tp3', 'hit_sl', 'closed']);
            } else {
                $query->where('status', $request->status);
            }
        }

        // Filter by action (BUY, SELL)
        if ($request->filled('action') && $request->action !== 'all') {
            $query->where('action', 'like', $request->action . '%');
        }

        // Filter by premium access
        if ($request->filled('is_premium') && $request->is_premium !== 'all') {
            $isPremium = filter_var($request->is_premium, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($isPremium !== null) {
                $query->where('is_premium', $isPremium);
            }
        }

        // Search query
        if ($request->filled('search')) {
            $search = '%' . trim($request->search) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('pair', 'like', $search)
                  ->orWhere('title', 'like', $search)
                  ->orWhere('analysis_notes', 'like', $search);
            });
        }

        // Sorting
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'pips_desc':
                $query->orderBy('result_pips', 'desc');
                break;
            case 'pips_asc':
                $query->orderBy('result_pips', 'asc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc')->orderBy('id', 'desc');
                break;
        }

        $perPage = min((int) $request->get('per_page', 15), 50);
        $paginated = $query->paginate($perPage);

        $defaultChart = 'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=800&auto=format&fit=crop&q=80';

        $items = collect($paginated->items())->map(function (Signal $signal) use ($defaultChart) {
            return [
                'id' => $signal->id,
                'title' => $signal->title,
                'market_type' => $signal->market_type,
                'market_type_label' => match ($signal->market_type) {
                    'forex' => 'Forex',
                    'commodities' => 'Komoditas',
                    'crypto' => 'Kripto',
                    'indices' => 'Indeks',
                    default => ucfirst($signal->market_type),
                },
                'market_type_color' => $signal->market_type_color,
                'pair' => $signal->pair,
                'timeframe' => $signal->timeframe,
                'action' => $signal->action,
                'action_color' => $signal->action_color,
                'entry_price' => (float) $signal->entry_price,
                'stop_loss' => (float) $signal->stop_loss,
                'take_profit_1' => (float) $signal->take_profit_1,
                'take_profit_2' => $signal->take_profit_2 ? (float) $signal->take_profit_2 : null,
                'take_profit_3' => $signal->take_profit_3 ? (float) $signal->take_profit_3 : null,
                'risk_reward_ratio' => $signal->risk_reward_ratio,
                'status' => $signal->status,
                'status_label' => $signal->status_label,
                'status_color' => $signal->status_color,
                'result_pips' => $signal->result_pips !== null ? (float) $signal->result_pips : null,
                'risk_percentage' => (float) ($signal->risk_percentage ?? 1.0),
                'analysis_notes' => $signal->analysis_notes,
                'chart_image_url' => $signal->chart_image_url ?: $defaultChart,
                'is_premium' => (bool) $signal->is_premium,
                'published_at' => $signal->published_at?->toIso8601String(),
                'closed_at' => $signal->closed_at?->toIso8601String(),
                'created_at' => $signal->created_at?->toIso8601String(),
                'mentor' => [
                    'id' => $signal->mentor?->id,
                    'name' => $signal->mentor?->name ?? 'Head of Trading Desk',
                    'email' => $signal->mentor?->email,
                    'avatar_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=120&auto=format&fit=crop&q=80',
                ],
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $items,
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }

    /**
     * Get aggregate statistics for signals dashboard.
     * GET /api/v1/signals/stats
     */
    public function stats(): JsonResponse
    {
        $activeCount = Signal::whereIn('status', ['active', 'pending'])->count();
        $totalSignals = Signal::count();

        $wonCount = Signal::whereIn('status', ['hit_tp1', 'hit_tp2', 'hit_tp3'])->count();
        $lostCount = Signal::where('status', 'hit_sl')->count();
        $resolvedCount = $wonCount + $lostCount;
        $winRate = $resolvedCount > 0 ? round(($wonCount / $resolvedCount) * 100, 1) : 80.0;

        $totalPips = (float) Signal::sum('result_pips');

        // Market category breakdown
        $commoditiesCount = Signal::where('market_type', 'commodities')->count();
        $forexCount = Signal::where('market_type', 'forex')->count();
        $cryptoCount = Signal::where('market_type', 'crypto')->count();
        $indicesCount = Signal::where('market_type', 'indices')->count();

        $totalCategorized = max(1, $commoditiesCount + $forexCount + $cryptoCount + $indicesCount);

        return response()->json([
            'success' => true,
            'data' => [
                'active_count' => $activeCount,
                'total_signals' => $totalSignals,
                'won_count' => $wonCount,
                'lost_count' => $lostCount,
                'win_rate' => $winRate,
                'net_pips' => $totalPips,
                'net_pips_formatted' => ($totalPips >= 0 ? '+' : '') . number_format($totalPips, 0, ',', '.') . ' Pips',
                'top_category' => 'Gold (XAUUSD) & Forex',
                'breakdown_description' => 'Komoditas 45% • Forex 35% • Kripto 20%',
            ],
        ]);
    }

    /**
     * Display the specified signal.
     * GET /api/v1/signals/{id}
     */
    public function show(int $id): JsonResponse
    {
        $signal = Signal::with(['mentor:id,name,email'])->findOrFail($id);

        $defaultChart = 'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=800&auto=format&fit=crop&q=80';

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $signal->id,
                'title' => $signal->title,
                'market_type' => $signal->market_type,
                'market_type_label' => match ($signal->market_type) {
                    'forex' => 'Forex',
                    'commodities' => 'Komoditas',
                    'crypto' => 'Kripto',
                    'indices' => 'Indeks',
                    default => ucfirst($signal->market_type),
                },
                'market_type_color' => $signal->market_type_color,
                'pair' => $signal->pair,
                'timeframe' => $signal->timeframe,
                'action' => $signal->action,
                'action_color' => $signal->action_color,
                'entry_price' => (float) $signal->entry_price,
                'stop_loss' => (float) $signal->stop_loss,
                'take_profit_1' => (float) $signal->take_profit_1,
                'take_profit_2' => $signal->take_profit_2 ? (float) $signal->take_profit_2 : null,
                'take_profit_3' => $signal->take_profit_3 ? (float) $signal->take_profit_3 : null,
                'risk_reward_ratio' => $signal->risk_reward_ratio,
                'status' => $signal->status,
                'status_label' => $signal->status_label,
                'status_color' => $signal->status_color,
                'result_pips' => $signal->result_pips !== null ? (float) $signal->result_pips : null,
                'risk_percentage' => (float) ($signal->risk_percentage ?? 1.0),
                'analysis_notes' => $signal->analysis_notes,
                'chart_image_url' => $signal->chart_image_url ?: $defaultChart,
                'is_premium' => (bool) $signal->is_premium,
                'published_at' => $signal->published_at?->toIso8601String(),
                'closed_at' => $signal->closed_at?->toIso8601String(),
                'created_at' => $signal->created_at?->toIso8601String(),
                'mentor' => [
                    'id' => $signal->mentor?->id,
                    'name' => $signal->mentor?->name ?? 'Head of Trading Desk',
                    'email' => $signal->mentor?->email,
                    'avatar_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=120&auto=format&fit=crop&q=80',
                ],
            ],
        ]);
    }
}
