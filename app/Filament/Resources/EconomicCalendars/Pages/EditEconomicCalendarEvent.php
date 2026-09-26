<?php

namespace App\Filament\Resources\EconomicCalendars\Pages;

use App\Domain\Market\Models\WidgetConfig;
use App\Filament\Resources\EconomicCalendars\EconomicCalendarResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;

class EditEconomicCalendarEvent extends EditRecord
{
    protected static string $resource = EconomicCalendarResource::class;

    public function getMaxContentWidth(): Width | string | null
    {
        return Width::Full;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview_live')
                ->label('👁️ Preview Widget')
                ->icon(Heroicon::OutlinedEye)
                ->color('warning')
                ->modalHeading(fn (WidgetConfig $record): string => 'Live Preview: ' . ($record->customer_id ? "Customer: {$record->customer?->name}" : 'Global Default'))
                ->modalContent(fn (WidgetConfig $record) => view('filament.resources.economic-calendars.preview-widget', [
                    'config' => $record,
                    'tradingViewConfig' => $record->toTradingViewConfig(),
                ]))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Tutup Preview')
                ->modalWidth('5xl'),

            DeleteAction::make()
                ->visible(fn (WidgetConfig $record): bool => $record->customer_id !== null),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
