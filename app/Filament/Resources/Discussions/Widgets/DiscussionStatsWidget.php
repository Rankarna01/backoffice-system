<?php

namespace App\Filament\Resources\Discussions\Widgets;

use App\Domain\Community\Enums\ModerationStatus;
use App\Domain\Community\Models\Discussion;
use App\Domain\Community\Models\DiscussionReply;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DiscussionStatsWidget extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $totalThreads = Discussion::count();
        $needsModerationCount = Discussion::whereIn('moderation_status', [
            ModerationStatus::PendingReview->value,
            ModerationStatus::Flagged->value,
        ])->count();
        $pinnedCount = Discussion::where('is_pinned', true)->count();
        $totalReplies = DiscussionReply::count();

        return [
            Stat::make('Total Thread Diskusi', (string) $totalThreads)
                ->description('Forum komunitas & tanya-jawab trader')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('primary'),

            Stat::make('Butuh Moderasi ⚠️', (string) $needsModerationCount)
                ->description($needsModerationCount > 0 ? 'Perlu tindakan review moderator' : 'Semua diskusi aman & disetujui')
                ->descriptionIcon('heroicon-m-shield-exclamation')
                ->color($needsModerationCount > 0 ? 'danger' : 'success'),

            Stat::make('Diskusi Disematkan 📌', (string) $pinnedCount)
                ->description('Panduan & pengumuman utama pinned')
                ->descriptionIcon('heroicon-m-bookmark')
                ->color('warning'),

            Stat::make('Total Komentar & Balasan', (string) $totalReplies)
                ->description('Interaksi aktif member & mentor')
                ->descriptionIcon('heroicon-m-chat-bubble-bottom-center-text')
                ->color('info'),
        ];
    }
}
