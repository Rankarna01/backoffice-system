<?php

namespace App\Filament\Resources\Reviews\Widgets;

use App\Domain\Content\Enums\ReviewStatus;
use App\Domain\Content\Models\Review;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ReviewStatsWidget extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $avgRating = Review::where('status', ReviewStatus::Approved)->avg('rating') ?: 5.0;
        $totalReviews = Review::count();
        $pendingCount = Review::where('status', ReviewStatus::Pending)->count();
        $featuredCount = Review::where('is_featured', true)->count();

        return [
            Stat::make('Rata-rata Rating', number_format($avgRating, 1) . ' / 5.0')
                ->description('Skor kepuasan siswa dari ulasan aktif')
                ->descriptionIcon('bx-star')
                ->color('warning'),

            Stat::make('Total Ulasan Masuk', (string) $totalReviews)
                ->description('Review kursus & materi dari murid')
                ->descriptionIcon('bx-message-square-detail')
                ->color('primary'),

            Stat::make('Menunggu Moderasi', (string) $pendingCount)
                ->description($pendingCount > 0 ? 'Perlu tindakan review admin' : 'Semua ulasan telah dimoderasi')
                ->descriptionIcon('bx-time')
                ->color($pendingCount > 0 ? 'danger' : 'success'),

            Stat::make('Ulasan Pilihan (Featured)', (string) $featuredCount)
                ->description('Ditampilkan di card depan kursus')
                ->descriptionIcon('bxs-badge-check')
                ->color('info'),
        ];
    }
}
