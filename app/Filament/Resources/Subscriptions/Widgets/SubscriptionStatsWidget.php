<?php

namespace App\Filament\Resources\Subscriptions\Widgets;

use App\Domain\Billing\Enums\SubscriptionStatus;
use App\Domain\Billing\Models\Plan;
use App\Domain\Billing\Models\Subscription;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SubscriptionStatsWidget extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $activeCount = Subscription::where('status', SubscriptionStatus::Active)->count();
        $expiringSoon = Subscription::expiringSoon(7)->count();
        $lifetimeCount = Subscription::where('status', SubscriptionStatus::Active)->whereNull('ends_at')->count();
        $totalPlans = Plan::where('is_active', true)->count();

        return [
            Stat::make('Langganan Aktif', (string) $activeCount)
                ->description('Member dengan akses aktif')
                ->descriptionIcon('bx-check-circle')
                ->color('success'),

            Stat::make('Segera Berakhir (7 Hari)', (string) $expiringSoon)
                ->description($expiringSoon > 0 ? 'Perlu follow-up perpanjangan' : 'Tidak ada yang segera habis')
                ->descriptionIcon('bx-time-five')
                ->color($expiringSoon > 0 ? 'warning' : 'gray'),

            Stat::make('Member Lifetime', (string) $lifetimeCount)
                ->description('Akses seumur hidup tanpa masa kedaluwarsa')
                ->descriptionIcon('bxs-crown')
                ->color('primary'),

            Stat::make('Paket Langganan', (string) $totalPlans)
                ->description('Pilihan tier keanggotaan aktif')
                ->descriptionIcon('bx-package')
                ->color('info'),
        ];
    }
}
