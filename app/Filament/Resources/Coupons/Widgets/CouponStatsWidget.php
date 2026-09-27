<?php

namespace App\Filament\Resources\Coupons\Widgets;

use App\Domain\Billing\Models\Coupon;
use App\Domain\Billing\Models\CouponRedemption;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CouponStatsWidget extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $activeCount = Coupon::where('is_active', true)->count();
        $totalClaims = CouponRedemption::count();
        $totalDiscountGiven = CouponRedemption::sum('discount_amount');
        $topCoupon = Coupon::orderByDesc('redemptions_count')->first();

        return [
            Stat::make('Kupon Aktif', (string) $activeCount)
                ->description('Kode promosi siap digunakan')
                ->descriptionIcon('bx-purchase-tag')
                ->color('success'),

            Stat::make('Total Pemakaian Kupon', (string) $totalClaims)
                ->description('Kali kupon diklaim oleh member')
                ->descriptionIcon('bx-check-double')
                ->color('primary'),

            Stat::make('Total Diskon Diberikan', 'Rp ' . number_format($totalDiscountGiven, 0, ',', '.'))
                ->description('Akumulasi potongan harga dinikmati murid')
                ->descriptionIcon('bx-gift')
                ->color('info'),

            Stat::make('Kupon Paling Populer', $topCoupon ? $topCoupon->code : '—')
                ->description($topCoupon ? "{$topCoupon->redemptions_count} kali dipakai" : 'Belum ada data')
                ->descriptionIcon('bx-star')
                ->color('warning'),
        ];
    }
}
