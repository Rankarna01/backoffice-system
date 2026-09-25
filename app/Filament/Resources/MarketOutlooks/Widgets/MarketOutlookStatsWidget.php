<?php

namespace App\Filament\Resources\MarketOutlooks\Widgets;

use App\Domain\Market\Models\MarketOutlook;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MarketOutlookStatsWidget extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $total = MarketOutlook::count();
        $published = MarketOutlook::where('status', 'published')->count();
        $bullishCount = MarketOutlook::where('sentiment', 'bullish')->count();
        $bearishCount = MarketOutlook::where('sentiment', 'bearish')->count();

        $dominant = $bullishCount >= $bearishCount ? 'Bullish Bias 🐂' : 'Bearish Bias 🐻';
        $dominantColor = $bullishCount >= $bearishCount ? 'success' : 'danger';

        $vipCount = MarketOutlook::where('is_premium', true)->count();
        $vipPercent = $total > 0 ? round(($vipCount / $total) * 100) : 0;

        return [
            Stat::make('Total Market Outlook', (string) $total)
                ->description("{$published} edisi terbit aktif di portal murid")
                ->descriptionIcon('heroicon-m-presentation-chart-line')
                ->color('primary'),

            Stat::make('Bias Sentimen Terkini', $dominant)
                ->description("{$bullishCount} Bullish • {$bearishCount} Bearish ulasan")
                ->descriptionIcon('heroicon-m-chart-pie')
                ->color($dominantColor),

            Stat::make('Akses Konten VIP', "{$vipPercent}% VIP")
                ->description("{$vipCount} artikel eksklusif member premium")
                ->descriptionIcon('heroicon-m-lock-closed')
                ->color('warning'),

            Stat::make('Cakupan Pasar', 'Multi-Asset Macro')
                ->description('Komoditas • Forex • Kripto • Indeks')
                ->descriptionIcon('heroicon-m-globe-alt')
                ->color('info'),
        ];
    }
}
