<?php

namespace App\Filament\Widgets;

use App\Domain\Notifications\Models\BroadcastNotification;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class NotificationStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $totalBroadcasts = BroadcastNotification::count();
        $sentBroadcasts = BroadcastNotification::where('status', 'sent')->count();
        $totalRecipients = (int) BroadcastNotification::sum('total_recipients');
        $totalReads = (int) BroadcastNotification::sum('read_count');

        $avgReadRate = $totalRecipients > 0 ? round(($totalReads / $totalRecipients) * 100, 1) : 0.0;

        return [
            Stat::make('Total Siaran Notifikasi', number_format($totalBroadcasts))
                ->description('Seluruh draft & siaran terdaftar')
                ->descriptionIcon('heroicon-m-bell-alert')
                ->color('primary'),

            Stat::make('Siaran Terkirim', number_format($sentBroadcasts))
                ->description('Berhasil disiarkan ke pengguna')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make('Jangkauan Penerima', number_format($totalRecipients))
                ->description('Total user yang menerima notifikasi')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('Rata-rata Dibaca', "{$avgReadRate}%")
                ->description('Rasio buka & interaksi notifikasi')
                ->descriptionIcon('heroicon-m-eye')
                ->color('warning'),
        ];
    }
}
