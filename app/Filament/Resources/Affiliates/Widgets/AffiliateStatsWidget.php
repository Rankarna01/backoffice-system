<?php

namespace App\Filament\Resources\Affiliates\Widgets;

use App\Domain\Billing\Enums\AffiliateStatus;
use App\Domain\Billing\Models\Affiliate;
use App\Domain\Billing\Models\Commission;
use App\Domain\Billing\Models\Payout;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AffiliateStatsWidget extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $activeAffiliates = Affiliate::where('status', AffiliateStatus::Approved)->count();
        $totalCommissions = Commission::sum('amount');
        $totalPaidOut = Payout::where('status', 'paid')->sum('amount');
        $pendingPayout = max(0, $totalCommissions - $totalPaidOut);

        return [
            Stat::make('Mitra Afiliasi Aktif', (string) $activeAffiliates)
                ->description('Partner aktif membagikan referral')
                ->descriptionIcon('bx-share-alt')
                ->color('primary'),

            Stat::make('Total Komisi Terkumpul', 'Rp ' . number_format($totalCommissions, 0, ',', '.'))
                ->description('Seluruh bagi hasil komisi mitra')
                ->descriptionIcon('bx-line-chart')
                ->color('info'),

            Stat::make('Saldo Menunggu Pencairan', 'Rp ' . number_format($pendingPayout, 0, ',', '.'))
                ->description('Hak komisi belum dicairkan ke mitra')
                ->descriptionIcon('bx-time-five')
                ->color($pendingPayout > 0 ? 'warning' : 'gray'),

            Stat::make('Total Payout Dicairkan', 'Rp ' . number_format($totalPaidOut, 0, ',', '.'))
                ->description('Dana komisi telah ditransfer ke bank mitra')
                ->descriptionIcon('bx-check-double')
                ->color('success'),
        ];
    }
}
