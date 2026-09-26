<?php

namespace App\Filament\Resources\Discussions\Pages;

use App\Filament\Resources\Discussions\DiscussionResource;
use App\Filament\Resources\Discussions\Widgets\DiscussionStatsWidget;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListDiscussions extends ListRecords
{
    protected static string $resource = DiscussionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Buat Thread Baru')
                ->icon('bx-plus-circle'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            DiscussionStatsWidget::class,
        ];
    }
}
