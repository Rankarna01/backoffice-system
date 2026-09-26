<?php

namespace App\Filament\Resources\EconomicCalendars\Pages;

use App\Domain\Market\Models\WidgetConfig;
use App\Filament\Resources\EconomicCalendars\EconomicCalendarResource;
use App\Filament\Resources\EconomicCalendars\Widgets\EconomicCalendarStatsWidget;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListEconomicCalendarEvents extends ListRecords
{
    protected static string $resource = EconomicCalendarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview_live_widget')
                ->label('👁️ Preview Live Widget TradingView')
                ->icon(Heroicon::OutlinedEye)
                ->color('warning')
                ->modalHeading('Live Preview Widget Kalender Ekonomi TradingView')
                ->modalDescription('Tampilan nyata script resmi TradingView (https://s3.tradingview.com/external-embedding/embed-widget-events.js) berdasarkan konfigurasi aktif.')
                ->modalContent(function () {
                    $config = WidgetConfig::getEffectiveConfig();
                    return view('filament.resources.economic-calendars.preview-widget', [
                        'config' => $config,
                        'tradingViewConfig' => $config->toTradingViewConfig(),
                    ]);
                })
                ->modalWidth('5xl'),

            CreateAction::make()
                ->label('Tambah Override Customer')
                ->icon(Heroicon::OutlinedPlusCircle),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            EconomicCalendarStatsWidget::class,
        ];
    }
}
