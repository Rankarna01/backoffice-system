<?php

namespace App\Filament\Resources\LiveSessions\Widgets;

use App\Domain\Community\Enums\LiveSessionStatus;
use App\Domain\Community\Models\LiveSession;
use App\Domain\Community\Models\LiveSessionRegistration;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LiveSessionStatsWidget extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $upcomingCount = LiveSession::where('status', LiveSessionStatus::Upcoming)->count();
        $liveNowCount = LiveSession::where('status', LiveSessionStatus::Live)->count();
        $totalRegistrations = LiveSessionRegistration::count();
        $recordedCount = LiveSession::whereNotNull('recording_url')->where('recording_url', '!=', '')->count();

        return [
            Stat::make('Sesi Akan Datang', (string) $upcomingCount)
                ->description('Webinar & live streaming terjadwal')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),

            Stat::make('Sedang Live Sekarang 🔴', (string) $liveNowCount)
                ->description($liveNowCount > 0 ? 'Room aktif & mentor sedang online' : 'Tidak ada sesi live saat ini')
                ->descriptionIcon('heroicon-m-signal')
                ->color($liveNowCount > 0 ? 'danger' : 'gray'),

            Stat::make('Total Pendaftar Member', (string) $totalRegistrations)
                ->description('Peserta terdaftar di semua sesi')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),

            Stat::make('Arsip Rekaman (Replay)', (string) $recordedCount)
                ->description('Video replay dapat diakses member')
                ->descriptionIcon('heroicon-m-play-circle')
                ->color('success'),
        ];
    }
}
