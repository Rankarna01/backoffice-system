<?php

namespace App\Filament\Resources\Media\Pages;

use App\Filament\Resources\Media\MediaResource;
use App\Filament\Resources\Media\Widgets\CloudflareR2StatsWidget;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMedia extends ListRecords
{
    protected static string $resource = MediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Upload File ke R2')
                ->icon('heroicon-m-cloud-arrow-up'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            CloudflareR2StatsWidget::class,
        ];
    }
}
