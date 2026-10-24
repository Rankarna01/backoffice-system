<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Billing\Models\Order;
use App\Domain\Community\Models\LiveSession;
use App\Domain\Learning\Models\Course;
use App\Domain\Trading\Models\JournalTrade;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    /**
     * Get Platform Overview Analytics
     */
    public function overview(Request $request): JsonResponse
    {
        $days = (int) $request->query('days', 30);
        $startDate = now()->subDays($days)->startOfDay();

        $grossRevenue = (float) Order::where('status', 'paid')
            ->where('paid_at', '>=', $startDate)
            ->sum('total');

        $totalPaidOrders = Order::where('status', 'paid')
            ->where('paid_at', '>=', $startDate)
            ->count();

        $totalOrders = Order::where('created_at', '>=', $startDate)->count();
        $paidRate = $totalOrders > 0 ? round(($totalPaidOrders / $totalOrders) * 100, 1) : 0;

        $newMembers = User::where('created_at', '>=', $startDate)->count();
        $totalMembers = User::count();

        $activeCourses = Course::where('status', 'published')->count();
        $totalSessions = LiveSession::count();

        // Average win rate from journal trades
        $winningTrades = JournalTrade::where('result', 'win')->count();
        $closedTrades = JournalTrade::whereIn('result', ['win', 'loss'])->count();
        $avgWinRate = $closedTrades > 0 ? round(($winningTrades / $closedTrades) * 100, 1) : 62.5;

        // Daily chart data for revenue & registrations
        $chartDates = [];
        $revenueData = [];
        $memberData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i * 4);
            $chartDates[] = $date->format('d M');
            $revenueData[] = round(Order::where('status', 'paid')->whereDate('paid_at', '<=', $date)->sum('total') / 1000000, 1);
            $memberData[] = User::whereDate('created_at', '<=', $date)->count();
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'metrics' => [
                    'gross_revenue' => $grossRevenue,
                    'total_paid_orders' => $totalPaidOrders,
                    'paid_rate' => $paidRate,
                    'new_members' => $newMembers,
                    'total_members' => $totalMembers,
                    'active_courses' => $activeCourses,
                    'total_sessions' => $totalSessions,
                    'avg_win_rate' => $avgWinRate,
                ],
                'chart' => [
                    'labels' => $chartDates,
                    'revenue' => $revenueData,
                    'members' => $memberData,
                ],
            ],
        ]);
    }
}
