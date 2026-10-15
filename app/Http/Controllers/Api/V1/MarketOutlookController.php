<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Market\Models\MarketOutlook;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MarketOutlookController extends Controller
{
    /**
     * Display a listing of market outlooks.
     * GET /api/v1/market-outlooks
     */
    public function index(Request $request): JsonResponse
    {
        $query = MarketOutlook::query()
            ->with(['mentor:id,name,email'])
            ->where('status', 'published');

        // Filter by market category
        if ($request->filled('market_category') && $request->market_category !== 'all') {
            $query->where('market_category', $request->market_category);
        }

        // Filter by sentiment
        if ($request->filled('sentiment') && $request->sentiment !== 'all') {
            $query->where('sentiment', $request->sentiment);
        }

        // Filter by time horizon
        if ($request->filled('time_horizon') && $request->time_horizon !== 'all') {
            $query->where('time_horizon', $request->time_horizon);
        }

        // Filter by premium access
        if ($request->filled('is_premium') && $request->is_premium !== 'all') {
            $isPremium = filter_var($request->is_premium, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($isPremium !== null) {
                $query->where('is_premium', $isPremium);
            }
        }

        // Search in title, summary, featured pairs
        if ($request->filled('search')) {
            $search = '%' . trim($request->search) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', $search)
                  ->orWhere('summary', 'like', $search)
                  ->orWhere('slug', 'like', $search);
            });
        }

        // Sorting
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'popular':
                $query->orderBy('views_count', 'desc');
                break;
            case 'oldest':
                $query->orderBy('published_at', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('published_at', 'desc')->orderBy('id', 'desc');
                break;
        }

        $perPage = min((int) $request->get('per_page', 9), 30);
        $paginated = $query->paginate($perPage);

        $defaultCover = 'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=800&auto=format&fit=crop&q=80';

        $items = collect($paginated->items())->map(function (MarketOutlook $outlook) use ($defaultCover) {
            return [
                'id' => $outlook->id,
                'title' => $outlook->title,
                'slug' => $outlook->slug,
                'summary' => $outlook->summary,
                'sentiment' => $outlook->sentiment,
                'sentiment_label' => $outlook->sentiment_label,
                'sentiment_color' => $outlook->sentiment_color,
                'market_category' => $outlook->market_category,
                'category_label' => match ($outlook->market_category) {
                    'commodities' => 'Komoditas',
                    'forex' => 'Forex',
                    'crypto' => 'Kripto',
                    'indices' => 'Indeks',
                    default => 'Multi-Asset',
                },
                'featured_pairs' => $outlook->featured_pairs ?? [],
                'time_horizon' => $outlook->time_horizon,
                'time_horizon_label' => match ($outlook->time_horizon) {
                    'weekly' => 'Mingguan',
                    'monthly' => 'Bulanan',
                    'quarterly' => 'Kuartalan',
                    default => 'Special Report',
                },
                'cover_image_url' => $outlook->cover_image_url ?: $defaultCover,
                'key_takeaways' => $outlook->key_takeaways ?? [],
                'support_resistance_levels' => $outlook->support_resistance_levels ?? [],
                'is_premium' => (bool) $outlook->is_premium,
                'status' => $outlook->status,
                'views_count' => (int) $outlook->views_count,
                'published_at' => $outlook->published_at?->toIso8601String(),
                'created_at' => $outlook->created_at?->toIso8601String(),
                'mentor' => [
                    'id' => $outlook->mentor?->id,
                    'name' => $outlook->mentor?->name ?? 'Lead Market Strategist',
                    'email' => $outlook->mentor?->email,
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
     * Get aggregate statistics for market outlooks dashboard.
     * GET /api/v1/market-outlooks/stats
     */
    public function stats(): JsonResponse
    {
        $total = MarketOutlook::count();
        $published = MarketOutlook::where('status', 'published')->count();
        $bullishCount = MarketOutlook::where('sentiment', 'bullish')->count();
        $bearishCount = MarketOutlook::where('sentiment', 'bearish')->count();
        $neutralCount = MarketOutlook::where('sentiment', 'neutral')->count();
        $volatileCount = MarketOutlook::where('sentiment', 'volatile')->count();

        $dominant = $bullishCount >= $bearishCount ? 'Bullish Bias' : 'Bearish Bias';
        $dominantColor = $bullishCount >= $bearishCount ? 'success' : 'danger';

        $vipCount = MarketOutlook::where('is_premium', true)->count();
        $vipPercent = $total > 0 ? round(($vipCount / $total) * 100) : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'total_outlooks' => $total,
                'published_count' => $published,
                'published_description' => "{$published} edisi terbit aktif di portal murid",
                'dominant_sentiment' => $dominant,
                'dominant_sentiment_color' => $dominantColor,
                'sentiment_description' => "{$bullishCount} Bullish • {$bearishCount} Bearish ulasan",
                'vip_percentage' => "{$vipPercent}% VIP",
                'vip_count' => $vipCount,
                'vip_description' => "{$vipCount} artikel eksklusif member premium",
                'market_coverage' => 'Multi-Asset Macro',
                'market_coverage_desc' => 'Komoditas • Forex • Kripto • Indeks',
            ],
        ]);
    }

    /**
     * Display the specified market outlook by slug.
     * GET /api/v1/market-outlooks/{slug}
     */
    public function show(string $slug): JsonResponse
    {
        $outlook = MarketOutlook::with(['mentor:id,name,email'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Increment views count
        $outlook->increment('views_count');

        $defaultCover = 'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=800&auto=format&fit=crop&q=80';

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $outlook->id,
                'title' => $outlook->title,
                'slug' => $outlook->slug,
                'summary' => $outlook->summary,
                'content' => $outlook->content,
                'sentiment' => $outlook->sentiment,
                'sentiment_label' => $outlook->sentiment_label,
                'sentiment_color' => $outlook->sentiment_color,
                'market_category' => $outlook->market_category,
                'category_label' => match ($outlook->market_category) {
                    'commodities' => 'Komoditas',
                    'forex' => 'Forex',
                    'crypto' => 'Kripto',
                    'indices' => 'Indeks',
                    default => 'Multi-Asset',
                },
                'featured_pairs' => $outlook->featured_pairs ?? [],
                'time_horizon' => $outlook->time_horizon,
                'time_horizon_label' => match ($outlook->time_horizon) {
                    'weekly' => 'Mingguan',
                    'monthly' => 'Bulanan',
                    'quarterly' => 'Kuartalan',
                    default => 'Special Report',
                },
                'cover_image_url' => $outlook->cover_image_url ?: $defaultCover,
                'key_takeaways' => $outlook->key_takeaways ?? [],
                'support_resistance_levels' => $outlook->support_resistance_levels ?? [],
                'is_premium' => (bool) $outlook->is_premium,
                'status' => $outlook->status,
                'views_count' => (int) $outlook->views_count,
                'published_at' => $outlook->published_at?->toIso8601String(),
                'created_at' => $outlook->created_at?->toIso8601String(),
                'mentor' => [
                    'id' => $outlook->mentor?->id,
                    'name' => $outlook->mentor?->name ?? 'Lead Market Strategist',
                    'email' => $outlook->mentor?->email,
                    'avatar_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=120&auto=format&fit=crop&q=80',
                ],
            ],
        ]);
    }
}
