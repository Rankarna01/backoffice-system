<?php

namespace App\Filament\Resources\Signals\Pages;

use App\Filament\Resources\Signals\SignalResource;
use App\Filament\Resources\Signals\Widgets\SignalStatsWidget;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSignals extends ListRecords
{
    protected static string $resource = SignalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Terbitkan Sinyal Baru')
                ->icon('heroicon-m-plus-circle'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            SignalStatsWidget::class,
        ];
    }
}
