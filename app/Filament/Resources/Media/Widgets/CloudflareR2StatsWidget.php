<?php

namespace App\Filament\Resources\Media\Widgets;

use App\Services\CloudflareR2Service;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CloudflareR2StatsWidget extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        /** @var CloudflareR2Service $r2 */
        $r2 = app(CloudflareR2Service::class);
        $status = $r2->getConnectionStatus();
        $usage = $r2->getStorageUsageSummary();

        return [
            Stat::make('Koneksi Cloudflare R2', $r2->isMockMode() ? 'Ready (Mock Mode)' : 'Connected (Active)')
                ->description("Bucket: {$status['bucket']} • Region {$status['region']}")
                ->descriptionIcon('heroicon-m-cloud')
                ->color($status['status_color']),

            Stat::make('Total Penyimpanan', $usage['total_formatted'])
                ->description("{$usage['total_files']} file terindeks • Zero Egress Fee")
                ->descriptionIcon('heroicon-m-server-stack')
                ->color('info'),

            Stat::make('Video Pelajaran', $usage['video_formatted'])
                ->description('Streaming CDN Anycast Edge')
                ->descriptionIcon('heroicon-m-video-camera')
                ->color('danger'),

            Stat::make('Dokumen & Gambar', "{$usage['doc_formatted']} / {$usage['image_formatted']}")
                ->description('PDF Modul, PPTX & Cover Kursus')
                ->descriptionIcon('heroicon-m-document-duplicate')
                ->color('warning'),
        ];
    }
}
