<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Market\Models\MarketNews;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MarketNewsController extends Controller
{
    /**
     * Display a listing of market news.
     * GET /api/v1/market-news
     */
    public function index(Request $request): JsonResponse
    {
        $query = MarketNews::query()
            ->with(['author:id,name,email'])
            ->where('status', 'published');

        // Filter by category
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        // Filter by impact level
        if ($request->filled('impact_level') && $request->impact_level !== 'all') {
            $query->where('impact_level', $request->impact_level);
        }

        // Filter by sentiment
        if ($request->filled('sentiment') && $request->sentiment !== 'all') {
            $query->where('sentiment', $request->sentiment);
        }

        // Filter by breaking news
        if ($request->filled('is_breaking') && $request->is_breaking !== 'all') {
            $isBreaking = filter_var($request->is_breaking, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($isBreaking !== null) {
                $query->where('is_breaking', $isBreaking);
            }
        }

        // Filter by featured
        if ($request->filled('is_featured') && $request->is_featured !== 'all') {
            $isFeatured = filter_var($request->is_featured, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($isFeatured !== null) {
                $query->where('is_featured', $isFeatured);
            }
        }

        // Search in title, summary, source, slug
        if ($request->filled('search')) {
            $search = '%' . trim($request->search) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', $search)
                  ->orWhere('summary', 'like', $search)
                  ->orWhere('source', 'like', $search)
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

        $perPage = min((int) $request->get('per_page', 12), 30);
        $paginated = $query->paginate($perPage);

        $defaultCover = 'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=800&auto=format&fit=crop&q=80';

        $items = collect($paginated->items())->map(function (MarketNews $news) use ($defaultCover) {
            return [
                'id' => $news->id,
                'title' => $news->title,
                'slug' => $news->slug,
                'summary' => $news->summary,
                'category' => $news->category,
                'category_label' => match ($news->category) {
                    'central_banks' => 'Bank Sentral',
                    'commodities' => 'Komoditas',
                    'forex' => 'Forex',
                    'crypto' => 'Kripto',
                    'indices' => 'Indeks Saham',
                    default => 'Ekonomi Makro',
                },
                'impact_level' => $news->impact_level,
                'impact_label' => $news->impact_label,
                'impact_color' => $news->impact_color,
                'sentiment' => $news->sentiment,
                'sentiment_label' => ucfirst($news->sentiment ?? 'Neutral'),
                'sentiment_color' => $news->sentiment_color,
                'source' => $news->source ?: 'Internal',
                'source_url' => $news->source_url,
                'cover_image_url' => $news->cover_image_url ?: $defaultCover,
                'related_symbols' => $news->related_symbols ?? [],
                'is_breaking' => (bool) $news->is_breaking,
                'is_featured' => (bool) $news->is_featured,
                'status' => $news->status,
                'views_count' => (int) $news->views_count,
                'published_at' => $news->published_at?->toIso8601String(),
                'created_at' => $news->created_at?->toIso8601String(),
                'author' => [
                    'id' => $news->author?->id,
                    'name' => $news->author?->name ?? 'Tim Analis Pasar',
                    'email' => $news->author?->email,
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
     * Get aggregate statistics for market news dashboard.
     * GET /api/v1/market-news/stats
     */
    public function stats(): JsonResponse
    {
        $total = MarketNews::count();
        $highImpactCount = MarketNews::where('impact_level', 'high')->count();
        $breakingCount = MarketNews::where('is_breaking', true)->count();
        $bullishCount = MarketNews::where('sentiment', 'bullish')->count();
        $bearishCount = MarketNews::where('sentiment', 'bearish')->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total_news' => $total,
                'total_description' => 'Agregasi berita pasar global terverifikasi',
                'high_impact_count' => $highImpactCount,
                'high_impact_description' => 'Katalis volatilitas merah (High Impact)',
                'breaking_count' => $breakingCount,
                'breaking_description' => 'Notifikasi push kilat ke member',
                'bullish_count' => $bullishCount,
                'bearish_count' => $bearishCount,
                'sentiment_ratio' => "{$bullishCount} Bullish / {$bearishCount} Bearish",
                'sentiment_description' => 'Rasio sentimen berita bullish vs bearish',
            ],
        ]);
    }

    /**
     * Display the specified news article by slug.
     * GET /api/v1/market-news/{slug}
     */
    public function show(string $slug): JsonResponse
    {
        $news = MarketNews::with(['author:id,name,email'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Increment views count
        $news->increment('views_count');

        $defaultCover = 'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=800&auto=format&fit=crop&q=80';

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $news->id,
                'title' => $news->title,
                'slug' => $news->slug,
                'summary' => $news->summary,
                'content' => $news->content,
                'category' => $news->category,
                'category_label' => match ($news->category) {
                    'central_banks' => 'Bank Sentral',
                    'commodities' => 'Komoditas',
                    'forex' => 'Forex',
                    'crypto' => 'Kripto',
                    'indices' => 'Indeks Saham',
                    default => 'Ekonomi Makro',
                },
                'impact_level' => $news->impact_level,
                'impact_label' => $news->impact_label,
                'impact_color' => $news->impact_color,
                'sentiment' => $news->sentiment,
                'sentiment_label' => ucfirst($news->sentiment ?? 'Neutral'),
                'sentiment_color' => $news->sentiment_color,
                'source' => $news->source ?: 'Internal',
                'source_url' => $news->source_url,
                'cover_image_url' => $news->cover_image_url ?: $defaultCover,
                'related_symbols' => $news->related_symbols ?? [],
                'is_breaking' => (bool) $news->is_breaking,
                'is_featured' => (bool) $news->is_featured,
                'status' => $news->status,
                'views_count' => (int) $news->views_count,
                'published_at' => $news->published_at?->toIso8601String(),
                'created_at' => $news->created_at?->toIso8601String(),
                'author' => [
                    'id' => $news->author?->id,
                    'name' => $news->author?->name ?? 'Tim Analis Pasar',
                    'email' => $news->author?->email,
                    'avatar_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=120&auto=format&fit=crop&q=80',
                ],
            ],
        ]);
    }
}
