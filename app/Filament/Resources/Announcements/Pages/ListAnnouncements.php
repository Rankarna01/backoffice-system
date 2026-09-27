<?php

namespace App\Filament\Resources\Announcements\Pages;

use App\Filament\Resources\Announcements\AnnouncementResource;
use App\Filament\Resources\Announcements\Widgets\AnnouncementStatsWidget;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAnnouncements extends ListRecords
{
    protected static string $resource = AnnouncementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Buat Pengumuman')
                ->icon('bx-plus'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            AnnouncementStatsWidget::class,
        ];
    }
}
