<?php

namespace App\Filament\Resources\Reviews\Pages;

use App\Filament\Resources\Reviews\ReviewResource;
use App\Filament\Resources\Reviews\Widgets\ReviewStatsWidget;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListReviews extends ListRecords
{
    protected static string $resource = ReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tulis Ulasan Baru')
                ->icon('bx-plus'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ReviewStatsWidget::class,
        ];
    }
}
