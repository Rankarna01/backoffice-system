<?php

namespace App\Filament\Resources\Faqs\Widgets;

use App\Domain\Content\Models\Faq;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FaqStatsWidget extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $publishedCount = Faq::where('is_published', true)->count();
        $categoriesCount = Faq::distinct('category')->count('category');

        return [
            Stat::make('FAQ Publikasi Aktif', (string) $publishedCount)
                ->description('Pertanyaan bantuan siap dibaca member')
                ->descriptionIcon('bx-check-double')
                ->color('success'),

            Stat::make('Kategori FAQ', (string) $categoriesCount)
                ->description('Topik seputar kursus, sinyal & billing')
                ->descriptionIcon('bx-category')
                ->color('info'),
        ];
    }
}
