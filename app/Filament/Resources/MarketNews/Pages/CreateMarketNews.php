<?php

namespace App\Filament\Resources\MarketNews\Pages;

use App\Filament\Resources\MarketNews\MarketNewsResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateMarketNews extends CreateRecord
{
    protected static string $resource = MarketNewsResource::class;

    public function getMaxContentWidth(): Width | string | null
    {
        return Width::Full;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
