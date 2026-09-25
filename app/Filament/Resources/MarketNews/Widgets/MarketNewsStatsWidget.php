<?php

namespace App\Filament\Resources\MarketNews\Widgets;

use App\Domain\Market\Models\MarketNews;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MarketNewsStatsWidget extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $total = MarketNews::count();
        $highImpactCount = MarketNews::where('impact_level', 'high')->count();
        $breakingCount = MarketNews::where('is_breaking', true)->count();
        $bullishCount = MarketNews::where('sentiment', 'bullish')->count();
        $bearishCount = MarketNews::where('sentiment', 'bearish')->count();

        return [
            Stat::make('Total Berita Finansial', (string) $total)
                ->description('Agregasi berita pasar global terverifikasi')
                ->descriptionIcon('heroicon-m-newspaper')
                ->color('primary'),

            Stat::make('Berita Berdampak Tinggi', (string) $highImpactCount)
                ->description('Katalis volatilitas merah (High Impact)')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),

            Stat::make('Flash / Breaking News', (string) $breakingCount)
                ->description('Notifikasi push kilat ke member')
                ->descriptionIcon('heroicon-m-bolt')
                ->color('warning'),

            Stat::make('Sentimen Berita', "{$bullishCount} 🐂 / {$bearishCount} 🐻")
                ->description('Rasio sentimen berita bullish vs bearish')
                ->descriptionIcon('heroicon-m-scale')
                ->color('info'),
        ];
    }
}
