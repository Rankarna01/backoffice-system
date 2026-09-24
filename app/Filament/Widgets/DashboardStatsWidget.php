<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        return [
            Stat::make('Revenue', 'Rp 48,6 jt')
                ->description('12,4% vs last period')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([20, 24, 28, 26, 34, 40, 48.6])
                ->color('primary'),

            Stat::make('New members', '1.284')
                ->description('8,1% vs last period')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([12, 16, 20, 18, 25, 29, 35])
                ->color('info'),

            Stat::make('Active subscriptions', '3.912')
                ->description('3,6% vs last period')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([22, 26, 30, 31, 33, 36, 39.12])
                ->color('success'),

            Stat::make('Course completion', '64%')
                ->description('1,2% vs last period')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->chart([70, 68, 67, 68, 66, 65, 64])
                ->color('danger'),
        ];
    }
}
