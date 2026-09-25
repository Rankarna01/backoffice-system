<?php

namespace App\Filament\Resources\EconomicCalendars\Pages;

use App\Filament\Resources\EconomicCalendars\EconomicCalendarResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;

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
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
