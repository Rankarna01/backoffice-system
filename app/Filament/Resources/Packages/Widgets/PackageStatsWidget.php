<?php

namespace App\Filament\Resources\Packages\Widgets;

use App\Domain\Billing\Enums\SubscriptionStatus;
use App\Domain\Billing\Models\Plan;
use App\Domain\Billing\Models\Subscription;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PackageStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $totalPlans = Plan::count();
        $activePlans = Plan::where('is_active', true)->count();
        
        $activeSubscribers = Subscription::where('status', SubscriptionStatus::Active->value ?? 'active')->count();
        
        $mostPopularPlan = Plan::withCount('subscriptions')
            ->orderByDesc('subscriptions_count')
            ->first();

        return [
            Stat::make('Total Paket Langganan', (string) $totalPlans)
                ->description("{$activePlans} paket berstatus aktif di web")
                ->descriptionIcon('heroicon-m-cube')
                ->color('primary'),

            Stat::make('Paket Aktif Publik', (string) $activePlans)
                ->description('Tersedia untuk dibeli member')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Total Pelanggan Aktif', number_format($activeSubscribers))
                ->description('Member dengan paket aktif')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),

            Stat::make('Paket Terpopuler', $mostPopularPlan ? $mostPopularPlan->name : '-')
                ->description($mostPopularPlan ? "{$mostPopularPlan->subscriptions_count} member berlangganan" : 'Belum ada transaksi')
                ->descriptionIcon('heroicon-m-fire')
                ->color('warning'),
        ];
    }
}
