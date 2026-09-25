<?php

namespace App\Filament\Resources\MarketNews\Pages;

use App\Filament\Resources\MarketNews\MarketNewsResource;
use App\Filament\Resources\MarketNews\Widgets\MarketNewsStatsWidget;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMarketNews extends ListRecords
{
    protected static string $resource = MarketNewsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tulis Berita Baru')
                ->icon('heroicon-m-plus-circle'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            MarketNewsStatsWidget::class,
        ];
    }
}
