<?php

namespace App\Filament\Resources\Orders\Widgets;

use App\Domain\Billing\Enums\OrderStatus;
use App\Domain\Billing\Models\Order;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OrderStatsWidget extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $paidTotal = Order::where('status', OrderStatus::Paid)->sum('total');
        $paidCount = Order::where('status', OrderStatus::Paid)->count();
        $pendingCount = Order::where('status', OrderStatus::Pending)->count();
        $totalOrders = Order::count();
        $conversionRate = $totalOrders > 0 ? round(($paidCount / $totalOrders) * 100, 1) : 0;

        return [
            Stat::make('Total Penjualan Lunas', 'Rp ' . number_format($paidTotal, 0, ',', '.'))
                ->description("{$paidCount} transaksi berhasil dibayar")
                ->descriptionIcon('bx-check-double')
                ->color('success'),

            Stat::make('Menunggu Pembayaran', (string) $pendingCount)
                ->description('Invoice pending belum diselesaikan')
                ->descriptionIcon('bx-time')
                ->color($pendingCount > 0 ? 'warning' : 'gray'),

            Stat::make('Tingkat Konversi Bayar', "{$conversionRate}%")
                ->description("Dari total {$totalOrders} pesanan masuk")
                ->descriptionIcon('bx-line-chart')
                ->color('primary'),

            Stat::make('Total Semua Pesanan', (string) $totalOrders)
                ->description('Seluruh pesanan tercatat di sistem')
                ->descriptionIcon('bx-receipt')
                ->color('info'),
        ];
    }
}
