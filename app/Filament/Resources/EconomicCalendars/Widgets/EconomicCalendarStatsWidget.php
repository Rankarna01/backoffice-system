<?php

namespace App\Filament\Resources\EconomicCalendars\Widgets;

use App\Domain\Market\Models\EconomicCalendarConfig;
use App\Domain\Market\Models\EconomicCalendarEvent;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EconomicCalendarStatsWidget extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $config = EconomicCalendarConfig::first();
        $isDemo = $config ? $config->is_demo : true;
        $status = $config ? $config->status : 'ready';

        $apiStatusText = $isDemo ? 'Demo Key (guest:guest)' : 'Live API Key Terpasang';
        $apiColor = $status === 'connected' ? 'success' : ($status === 'error' ? 'danger' : 'info');

        $totalEvents = EconomicCalendarEvent::count();
        $highImpactCount = EconomicCalendarEvent::where('impact_level', 'high')->count();
        $usdCount = EconomicCalendarEvent::where('currency', 'USD')->count();

        return [
            Stat::make('Status API Trading Economics', $apiStatusText)
                ->description($isDemo ? 'Mode demo kuota publik • Masukkan API key pribadi' : 'API Key aktif terhubung ke Trading Economics')
                ->descriptionIcon('heroicon-m-key')
                ->color($apiColor),

            Stat::make('Rilis High Impact 🔴', (string) $highImpactCount)
                ->description('Katalis volatilitas merah: NFP, CPI, Suku Bunga')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),

            Stat::make('Total Event Terjadwal', (string) $totalEvents)
                ->description('Jadwal rilis makro ekonomi global')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary'),

            Stat::make('Mata Uang USD Dominan', "{$usdCount} Rilis")
                ->description('Fokus dampak Dolar AS & Emas XAUUSD')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('warning'),
        ];
    }
}
