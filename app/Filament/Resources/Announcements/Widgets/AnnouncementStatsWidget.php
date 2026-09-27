<?php

namespace App\Filament\Resources\Announcements\Widgets;

use App\Domain\Content\Enums\AnnouncementStatus;
use App\Domain\Content\Enums\AnnouncementType;
use App\Domain\Content\Models\Announcement;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AnnouncementStatsWidget extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $publishedCount = Announcement::where('status', AnnouncementStatus::Published)->count();
        $pinnedCount = Announcement::where('is_pinned', true)->count();
        $promoCount = Announcement::where('type', AnnouncementType::Promo)->count();
        $urgentCount = Announcement::whereIn('type', [AnnouncementType::Warning, AnnouncementType::Maintenance])->count();

        return [
            Stat::make('Pengumuman Tayang', (string) $publishedCount)
                ->description('Siaran aktif tampil di dashboard murid')
                ->descriptionIcon('bx-broadcast')
                ->color('success'),

            Stat::make('Disematkan (Pinned)', (string) $pinnedCount)
                ->description('Pengumuman prioritas di bagian teratas')
                ->descriptionIcon('bx-bookmark')
                ->color('warning'),

            Stat::make('Promo & Event', (string) $promoCount)
                ->description('Siaran promosi diskon dan webinar')
                ->descriptionIcon('bx-purchase-tag')
                ->color('info'),

            Stat::make('Peringatan / Maintenance', (string) $urgentCount)
                ->description($urgentCount > 0 ? 'Pemberitahuan penting operasional' : 'Tidak ada peringatan kritis')
                ->descriptionIcon('bx-error-circle')
                ->color($urgentCount > 0 ? 'danger' : 'gray'),
        ];
    }
}
