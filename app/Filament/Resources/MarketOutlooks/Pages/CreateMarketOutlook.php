<?php

namespace App\Filament\Resources\MarketOutlooks\Pages;

use App\Filament\Resources\MarketOutlooks\MarketOutlookResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateMarketOutlook extends CreateRecord
{
    protected static string $resource = MarketOutlookResource::class;

    public function getMaxContentWidth(): Width | string | null
    {
        return Width::Full;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
