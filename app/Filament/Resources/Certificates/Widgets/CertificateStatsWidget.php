<?php

namespace App\Filament\Resources\Certificates\Widgets;

use App\Domain\Learning\Models\Certificate;
use App\Domain\Learning\Models\Course;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CertificateStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalCertificates = Certificate::count();
        $totalStudents = Certificate::distinct('user_id')->count('user_id');
        $certifiedCourses = Course::where('has_certificate', true)->count();
        $recentIssued = Certificate::where('issued_at', '>=', now()->subDays(30))->count();

        return [
            Stat::make('Total Sertifikat Diterbitkan', number_format($totalCertificates))
                ->description('Pencapaian kelulusan murid')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('primary'),

            Stat::make('Siswa Tersertifikasi', number_format($totalStudents))
                ->description('Murid aktif yang lulus')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),

            Stat::make('Kursus Bersertifikat', number_format($certifiedCourses))
                ->description('Kurikulum dengan sertifikat')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('warning'),

            Stat::make('Penerbitan 30 Hari Terakhir', number_format($recentIssued))
                ->description('Aktivitas kelulusan terbaru')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('info'),
        ];
    }
}
