<?php

namespace App\Filament\Resources\Payments\Widgets;

use App\Domain\Billing\Enums\PaymentStatus;
use App\Domain\Billing\Models\Payment;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PaymentStatsWidget extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $paidTotal = Payment::where('status', PaymentStatus::Paid)->sum('amount');
        $feeTotal = Payment::where('status', PaymentStatus::Paid)->sum('fee');
        $paidCount = Payment::where('status', PaymentStatus::Paid)->count();
        $pendingCount = Payment::where('status', PaymentStatus::Pending)->count();

        return [
            Stat::make('Total Dana Diterima', 'Rp ' . number_format($paidTotal, 0, ',', '.'))
                ->description('Settlement pembayaran berhasil')
                ->descriptionIcon('bx-wallet')
                ->color('success'),

            Stat::make('Potongan Biaya Gateway', 'Rp ' . number_format($feeTotal, 0, ',', '.'))
                ->description('Estimasi MDR & admin gateway fee')
                ->descriptionIcon('bx-receipt')
                ->color('gray'),

            Stat::make('Transaksi Berhasil', (string) $paidCount)
                ->description('Invoice terbayar & terverifikasi')
                ->descriptionIcon('bx-check-shield')
                ->color('primary'),

            Stat::make('Menunggu Pembayaran', (string) $pendingCount)
                ->description('Sesi checkout payment belum selesai')
                ->descriptionIcon('bx-time')
                ->color($pendingCount > 0 ? 'warning' : 'gray'),
        ];
    }
}
