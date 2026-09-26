<?php

namespace App\Filament\Resources\LiveSessions\Pages;

use App\Filament\Resources\LiveSessions\LiveSessionResource;
use App\Filament\Resources\LiveSessions\Widgets\LiveSessionStatsWidget;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListLiveSessions extends ListRecords
{
    protected static string $resource = LiveSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Jadwalkan Sesi Live')
                ->icon(Heroicon::OutlinedPlusCircle),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            LiveSessionStatsWidget::class,
        ];
    }
}
