<?php

namespace App\Filament\Resources\Testimonials\Pages;

use App\Filament\Resources\Testimonials\TestimonialResource;
use App\Filament\Resources\Testimonials\Widgets\TestimonialStatsWidget;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTestimonials extends ListRecords
{
    protected static string $resource = TestimonialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Testimoni')
                ->icon('bx-plus'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            TestimonialStatsWidget::class,
        ];
    }
}
