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
        $status = $config ? $config->status : 'ready';
        $providerName = $config ? ($config->provider === 'fcsapi' ? 'FCS API' : 'Trading Economics') : 'FCS API';
        $apiStatusText = $config && $config->api_key ? 'API Key Terpasang & Siap' : 'Belum Ada API Key';
        $apiColor = $status === 'connected' ? 'success' : ($status === 'error' ? 'danger' : 'info');

        $totalEvents = EconomicCalendarEvent::count();
        $highImpactCount = EconomicCalendarEvent::where('impact_level', 'high')->count();
        $usdCount = EconomicCalendarEvent::where('currency', 'USD')->count();

        return [
            Stat::make("Status Provider ({$providerName})", $apiStatusText)
                ->description($status === 'connected' ? 'Terhubung aktif ke fcsapi.com v4' : 'Klik tombol Test Hubungi API')
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
