<?php

namespace App\Filament\Resources\Testimonials\Widgets;

use App\Domain\Content\Models\Testimonial;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TestimonialStatsWidget extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $publishedCount = Testimonial::where('is_published', true)->count();
        $featuredCount = Testimonial::where('is_featured', true)->count();
        $avgRating = Testimonial::avg('rating') ?: 5.0;

        return [
            Stat::make('Testimoni Tayang', (string) $publishedCount)
                ->description('Social proof aktif di landing page')
                ->descriptionIcon('bx-check-double')
                ->color('success'),

            Stat::make('Testimoni Utama (Featured)', (string) $featuredCount)
                ->description('Disorot di hero banner & sales page')
                ->descriptionIcon('bxs-badge-check')
                ->color('primary'),

            Stat::make('Skor Kepuasan Rata-rata', number_format($avgRating, 1) . ' / 5.0')
                ->description('Kepercayaan murid & alumni')
                ->descriptionIcon('bx-star')
                ->color('warning'),
        ];
    }
}
