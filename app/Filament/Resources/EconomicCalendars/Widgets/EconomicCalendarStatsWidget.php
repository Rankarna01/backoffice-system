<?php

namespace App\Filament\Resources\EconomicCalendars\Widgets;

use App\Domain\Market\Models\WidgetConfig;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EconomicCalendarStatsWidget extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $globalConfig = WidgetConfig::getEffectiveConfig();
        $totalConfigs = WidgetConfig::count();
        $customerOverrides = WidgetConfig::whereNotNull('customer_id')->count();

        $themeText = ($globalConfig->color_theme === 'dark') ? 'Dark 🌙' : 'Light ☀️';
        $currencies = is_array($globalConfig->currencies) ? $globalConfig->currencies : ['USD', 'EUR', 'GBP'];
        $currencyCount = count($currencies);

        $importanceLabel = match ($globalConfig->importance_filter) {
            '1' => 'High Only 🔴',
            '0,1' => 'Med & High 🟡🔴',
            default => 'Semua Dampak 🟢🟡🔴',
        };

        return [
            Stat::make('Official Widget TradingView', $globalConfig->is_active ? 'Status Aktif ⚡' : 'Nonaktif ✕')
                ->description('Embed script: embed-widget-events.js')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color($globalConfig->is_active ? 'success' : 'danger'),

            Stat::make('Tema & Format Tampilan', $themeText)
                ->description("Ukuran: {$globalConfig->width} × {$globalConfig->height}px • Locale: {$globalConfig->locale}")
                ->descriptionIcon('heroicon-m-paint-brush')
                ->color('primary'),

            Stat::make('Filter Volatilitas / Dampak', $importanceLabel)
                ->description('Filter importance resmi TradingView')
                ->descriptionIcon('heroicon-m-funnel')
                ->color('warning'),

            Stat::make('Cakupan Konfigurasi', "{$totalConfigs} Konfigurasi")
                ->description("1 Global Default + {$customerOverrides} Override Customer")
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),
        ];
    }
}
