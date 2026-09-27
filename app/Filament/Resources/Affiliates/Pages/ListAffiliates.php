<?php

namespace App\Filament\Resources\Affiliates\Pages;

use App\Filament\Resources\Affiliates\AffiliateResource;
use App\Filament\Resources\Affiliates\Widgets\AffiliateStatsWidget;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAffiliates extends ListRecords
{
    protected static string $resource = AffiliateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Daftarkan Mitra Baru')
                ->icon('bx-plus'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            AffiliateStatsWidget::class,
        ];
    }
}
