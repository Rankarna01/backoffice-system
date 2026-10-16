<?php

namespace App\Filament\Resources\LiveMarketCharts\Widgets;

use App\Domain\Trading\Models\PaperTradingAccount;
use App\Domain\Trading\Models\PaperTradingPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LiveMarketChartStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalTraders = PaperTradingAccount::count();
        $totalBalance = (float) PaperTradingAccount::sum('balance');
        $totalOpenPositions = PaperTradingPosition::where('status', 'open')->count();
        $totalRealizedPnl = (float) PaperTradingPosition::where('status', 'closed')->sum('profit_loss');

        return [
            Stat::make('Total Trader Simulasi', number_format($totalTraders))
                ->description('Member aktif paper trading')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Total Saldo Beredar', '$' . number_format($totalBalance, 2))
                ->description('Modal simulasi seluruh member')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Posisi Terbuka Aktif', number_format($totalOpenPositions))
                ->description('Order BUY / SELL live saat ini')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('warning'),

            Stat::make('Kumulatif Realized P/L', ($totalRealizedPnl >= 0 ? '+$' : '-$') . number_format(abs($totalRealizedPnl), 2))
                ->description('Hasil transaksi yang ditutup')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color($totalRealizedPnl >= 0 ? 'success' : 'danger'),
        ];
    }
}
