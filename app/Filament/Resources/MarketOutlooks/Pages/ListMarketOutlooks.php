<?php

namespace App\Filament\Resources\MarketOutlooks\Pages;

use App\Filament\Resources\MarketOutlooks\MarketOutlookResource;
use App\Filament\Resources\MarketOutlooks\Widgets\MarketOutlookStatsWidget;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMarketOutlooks extends ListRecords
{
    protected static string $resource = MarketOutlookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tulis Outlook Baru')
                ->icon('heroicon-m-plus-circle'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            MarketOutlookStatsWidget::class,
        ];
    }
}
