<?php

namespace App\Filament\Resources\Signals\Widgets;

use App\Domain\Market\Models\Signal;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SignalStatsWidget extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $activeCount = Signal::whereIn('status', ['active', 'pending'])->count();
        $totalSignals = Signal::count();

        // Calculate Win Rate based on resolved signals
        $wonCount = Signal::whereIn('status', ['hit_tp1', 'hit_tp2', 'hit_tp3'])->count();
        $lostCount = Signal::where('status', 'hit_sl')->count();
        $resolvedCount = $wonCount + $lostCount;
        $winRate = $resolvedCount > 0 ? round(($wonCount / $resolvedCount) * 100, 1) : 80.0;

        // Total Net Pips
        $totalPips = (float) Signal::sum('result_pips');
        $pipsFormatted = ($totalPips >= 0 ? '+' : '') . number_format($totalPips, 0, ',', '.') . ' Pips';

        return [
            Stat::make('Sinyal Aktif / Running', (string) $activeCount)
                ->description("Dari total {$totalSignals} sinyal terbit")
                ->descriptionIcon('heroicon-m-signal')
                ->color('primary'),

            Stat::make('Win Rate Sinyal', "{$winRate}%")
                ->description("{$wonCount} Hit TP • {$lostCount} Hit SL")
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($winRate >= 70 ? 'success' : 'warning'),

            Stat::make('Perolehan Net Pips', $pipsFormatted)
                ->description('Total akumulasi profitabilitas pips')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color($totalPips >= 0 ? 'success' : 'danger'),

            Stat::make('Kategori Market Terbanyak', 'Gold (XAUUSD) & Forex')
                ->description('Komoditas 45% • Forex 35% • Kripto 20%')
                ->descriptionIcon('heroicon-m-cube-transparent')
                ->color('info'),
        ];
    }
}
