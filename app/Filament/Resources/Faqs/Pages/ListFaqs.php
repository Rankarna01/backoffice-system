<?php

namespace App\Filament\Resources\Faqs\Pages;

use App\Filament\Resources\Faqs\FaqResource;
use App\Filament\Resources\Faqs\Widgets\FaqStatsWidget;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFaqs extends ListRecords
{
    protected static string $resource = FaqResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah FAQ')
                ->icon('bx-plus'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            FaqStatsWidget::class,
        ];
    }
}
