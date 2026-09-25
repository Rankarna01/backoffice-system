<?php

namespace App\Filament\Resources\Signals\Pages;

use App\Filament\Resources\Signals\SignalResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateSignal extends CreateRecord
{
    protected static string $resource = SignalResource::class;

    public function getMaxContentWidth(): Width | string | null
    {
        return Width::Full;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['published_at'] = now();
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
