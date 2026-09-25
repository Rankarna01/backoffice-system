<?php

namespace App\Filament\Resources\EconomicCalendars\Pages;

use App\Filament\Resources\EconomicCalendars\EconomicCalendarResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateEconomicCalendarEvent extends CreateRecord
{
    protected static string $resource = EconomicCalendarResource::class;

    public function getMaxContentWidth(): Width | string | null
    {
        return Width::Full;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
