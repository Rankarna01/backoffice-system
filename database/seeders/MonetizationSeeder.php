<?php

namespace Database\Seeders;

use App\Domain\Billing\Enums\AffiliateStatus;
use App\Domain\Billing\Enums\CouponType;
use App\Domain\Billing\Enums\OrderStatus;
use App\Domain\Billing\Enums\PaymentGateway;
use App\Domain\Billing\Enums\PaymentStatus;
use App\Domain\Billing\Enums\SubscriptionStatus;
use App\Domain\Billing\Models\Affiliate;
use App\Domain\Billing\Models\Commission;
use App\Domain\Billing\Models\Coupon;
use App\Domain\Billing\Models\CouponRedemption;
use App\Domain\Billing\Models\Order;
use App\Domain\Billing\Models\OrderItem;
use App\Domain\Billing\Models\Payment;
use App\Domain\Billing\Models\Payout;
use App\Domain\Billing\Models\Plan;
use App\Domain\Billing\Models\Subscription;
use App\Domain\Learning\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MonetizationSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $studentBudi = User::where('email', 'budi@student.com')->first() ?: $admin;
        $mentorAlex = User::where('email', 'alex@tradingedu.com')->first() ?: $admin;
        $course = Course::first();

        // 1. Plans
        $planMonthly = Plan::updateOrCreate(
            ['slug' => 'pro-trader-monthly'],
            [
                'name' => 'Pro Trader Bulanan',
                'description' => 'Akses penuh ke semua sinyal trading harian, live webinar mingguan, dan seluruh materi kursus fundamental.',
                'type' => 'duration',
                'duration_days' => 30,
                'price' => 499000,
                'compare_at_price' => 699000,
                'currency' => 'IDR',
                'features' => [
                    'Semua Sinyal VIP Forex, Gold & Kripto',
                    'Akses ke Seluruh Modul & Video Kursus',
                    'Sesi Live Trading & Bedah Market Mingguan',
                    'Forum Diskusi Eksklusif Komunitas Pro',
                    'Kuota 100 Tanya-Jawab AI Assistant per Bulan',
                ],
                'includes_all_courses' => true,
                'includes_signals' => true,
                'ai_monthly_quota' => 100,
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        $planAnnual = Plan::updateOrCreate(
            ['slug' => 'pro-trader-annual'],
            [
                'name' => 'Pro Trader Tahunan (Best Value)',
                'description' => 'Paket langganan paling hemat dengan benefit tambahan sesi 1-on-1 review bersama master mentor.',
                'type' => 'duration',
                'duration_days' => 365,
                'price' => 3999000,
                'compare_at_price' => 5988000,
                'currency' => 'IDR',
                'features' => [
                    'Hemat 33% Dibandingkan Paket Bulanan',
                    'Semua Fitur Pro Trader Tanpa Batas',
                    '1x Sesi 1-on-1 Evaluasi Jurnal Trading',
                    'Indikator & Template Chart Custom TradingView',
                    'Unlimited AI Assistant Mentorship',
                ],
                'includes_all_courses' => true,
                'includes_signals' => true,
                'ai_monthly_quota' => 500,
                'is_active' => true,
                'sort_order' => 2,
            ]
        );

        $planLifetime = Plan::updateOrCreate(
            ['slug' => 'vip-lifetime-pass'],
            [
                'name' => 'VIP Lifetime Pass',
                'description' => 'Akses seumur hidup ke seluruh ekosistem TradingEdu termasuk seluruh rilis materi masa depan.',
                'type' => 'lifetime',
                'duration_days' => null,
                'price' => 9999000,
                'compare_at_price' => 14999000,
                'currency' => 'IDR',
                'features' => [
                    'Akses Seumur Hidup Tanpa Perpanjangan',
                    'Private Inner Circle Group bersama Mentor',
                    'Prioritas Akses Sesi Live Offline & Online',
                    'Garansi Update Materi Baru Selamanya',
                ],
                'includes_all_courses' => true,
                'includes_signals' => true,
                'ai_monthly_quota' => 9999,
                'is_active' => true,
                'sort_order' => 3,
            ]
        );

        // 2. Coupons
        $coupon1 = Coupon::updateOrCreate(
            ['code' => 'TRADINGPRO20'],
            [
                'name' => 'Diskon Peluncuran 20%',
                'type' => CouponType::Percent,
                'value' => 20,
                'max_discount' => 500000,
                'min_order' => 400000,
                'starts_at' => now()->subMonth(),
                'ends_at' => now()->addMonths(3),
                'max_redemptions' => 100,
                'redemptions_count' => 14,
                'max_per_user' => 1,
                'applies_to' => 'all',
                'is_active' => true,
            ]
        );

        $coupon2 = Coupon::updateOrCreate(
            ['code' => 'HEMAT100RB'],
            [
                'name' => 'Potongan Langsung Rp 100.000',
                'type' => CouponType::Fixed,
                'value' => 100000,
                'max_discount' => 100000,
                'min_order' => 450000,
                'starts_at' => now()->subWeeks(2),
                'ends_at' => now()->addMonth(),
                'max_redemptions' => 50,
                'redemptions_count' => 6,
                'max_per_user' => 1,
                'applies_to' => 'plans',
                'is_active' => true,
            ]
        );

        // 3. Affiliates
        $affiliateAlex = Affiliate::updateOrCreate(
            ['code' => 'ALEX10'],
            [
                'user_id' => $mentorAlex->id,
                'status' => AffiliateStatus::Approved,
                'commission_rate' => 20.00,
                'payout_details' => [
                    'bank_name' => 'BCA',
                    'account_number' => '8820192831',
                    'account_holder' => 'Alex Wijaya',
                ],
                'total_earnings' => 1599600,
                'total_paid' => 1000000,
                'approved_at' => now()->subMonths(2),
            ]
        );

        // 4. Subscriptions
        if ($studentBudi) {
            Subscription::updateOrCreate(
                ['user_id' => $studentBudi->id, 'plan_id' => $planAnnual->id],
                [
                    'uuid' => (string) Str::uuid(),
                    'status' => SubscriptionStatus::Active,
                    'starts_at' => now()->subMonths(1),
                    'ends_at' => now()->addMonths(11),
                    'auto_renew' => true,
                    'notes' => 'Aktivasi via paket tahunan',
                ]
            );
        }

        if ($admin) {
            Subscription::updateOrCreate(
                ['user_id' => $admin->id, 'plan_id' => $planLifetime->id],
                [
                    'uuid' => (string) Str::uuid(),
                    'status' => SubscriptionStatus::Active,
                    'starts_at' => now()->subMonths(3),
                    'ends_at' => null, // Lifetime
                    'auto_renew' => false,
                    'notes' => 'Internal Super Admin Lifetime Pass',
                ]
            );
        }

        // 5. Orders, Order Items, Payments & Commissions
        if ($studentBudi) {
            // Order 0: Prior Month Annual Plan (Komisi sudah dicairkan sebagian)
            $order0 = Order::updateOrCreate(
                ['number' => 'ORD-20260801-PRO00'],
                [
                    'uuid' => (string) Str::uuid(),
                    'user_id' => $studentBudi->id,
                    'status' => OrderStatus::Paid,
                    'currency' => 'IDR',
                    'subtotal' => 3999000,
                    'discount_total' => 0,
                    'tax_total' => 0,
                    'total' => 3999000,
                    'affiliate_id' => $affiliateAlex->id,
                    'referral_code' => 'ALEX10',
                    'paid_at' => now()->subMonths(2),
                    'expires_at' => now()->subMonths(2)->addDay(),
                ]
            );

            OrderItem::updateOrCreate(
                ['order_id' => $order0->id, 'purchasable_id' => $planAnnual->id],
                [
                    'purchasable_type' => 'plan',
                    'name' => 'Pro Trader Tahunan (Best Value)',
                    'unit_price' => 3999000,
                    'quantity' => 1,
                    'discount' => 0,
                    'total' => 3999000,
                ]
            );

            Payment::updateOrCreate(
                ['order_id' => $order0->id],
                [
                    'uuid' => (string) Str::uuid(),
                    'gateway' => PaymentGateway::Midtrans,
                    'gateway_ref' => 'MID-BCAVA-20260801-77112',
                    'method' => 'BCA Virtual Account',
                    'status' => PaymentStatus::Paid,
                    'amount' => 3999000,
                    'fee' => 4400,
                    'currency' => 'IDR',
                    'paid_at' => now()->subMonths(2),
                ]
            );

            Commission::updateOrCreate(
                ['order_id' => $order0->id],
                [
                    'affiliate_id' => $affiliateAlex->id,
                    'amount' => 799800,
                    'rate' => 20.00,
                    'status' => 'paid',
                    'available_at' => now()->subMonths(1),
                ]
            );

            // Order 1: Annual Plan (Lunas)
            $order1 = Order::updateOrCreate(
                ['number' => 'ORD-20260901-PRO01'],
                [
                    'uuid' => (string) Str::uuid(),
                    'user_id' => $studentBudi->id,
                    'status' => OrderStatus::Paid,
                    'currency' => 'IDR',
                    'subtotal' => 3999000,
                    'discount_total' => 0,
                    'tax_total' => 0,
                    'total' => 3999000,
                    'affiliate_id' => $affiliateAlex->id,
                    'referral_code' => 'ALEX10',
                    'paid_at' => now()->subMonths(1),
                    'expires_at' => now()->subMonths(1)->addDay(),
                ]
            );

            OrderItem::updateOrCreate(
                ['order_id' => $order1->id, 'purchasable_id' => $planAnnual->id],
                [
                    'purchasable_type' => 'plan',
                    'name' => 'Pro Trader Tahunan (Best Value)',
                    'unit_price' => 3999000,
                    'quantity' => 1,
                    'discount' => 0,
                    'total' => 3999000,
                ]
            );

            Payment::updateOrCreate(
                ['order_id' => $order1->id],
                [
                    'uuid' => (string) Str::uuid(),
                    'gateway' => PaymentGateway::Midtrans,
                    'gateway_ref' => 'MID-BCAVA-20260901-88912',
                    'method' => 'BCA Virtual Account',
                    'status' => PaymentStatus::Paid,
                    'amount' => 3999000,
                    'fee' => 4400,
                    'currency' => 'IDR',
                    'paid_at' => now()->subMonths(1),
                ]
            );

            Commission::updateOrCreate(
                ['order_id' => $order1->id],
                [
                    'affiliate_id' => $affiliateAlex->id,
                    'amount' => 799800, // 20%
                    'rate' => 20.00,
                    'status' => 'approved',
                    'available_at' => now()->subDays(14),
                ]
            );

            // Order 2: Course with Coupon (Lunas)
            if ($course) {
                $order2 = Order::updateOrCreate(
                    ['number' => 'ORD-20260915-CRS02'],
                    [
                        'uuid' => (string) Str::uuid(),
                        'user_id' => $studentBudi->id,
                        'status' => OrderStatus::Paid,
                        'currency' => 'IDR',
                        'subtotal' => 500000,
                        'discount_total' => 100000,
                        'tax_total' => 0,
                        'total' => 400000,
                        'coupon_id' => $coupon1->id,
                        'paid_at' => now()->subDays(12),
                        'expires_at' => now()->subDays(11),
                    ]
                );

                OrderItem::updateOrCreate(
                    ['order_id' => $order2->id, 'purchasable_id' => $course->id],
                    [
                        'purchasable_type' => 'course',
                        'name' => $course->title,
                        'unit_price' => 500000,
                        'quantity' => 1,
                        'discount' => 100000,
                        'total' => 400000,
                    ]
                );

                Payment::updateOrCreate(
                    ['order_id' => $order2->id],
                    [
                        'uuid' => (string) Str::uuid(),
                        'gateway' => PaymentGateway::Midtrans,
                        'gateway_ref' => 'MID-QRIS-20260915-77881',
                        'method' => 'QRIS GoPay / OVO',
                        'status' => PaymentStatus::Paid,
                        'amount' => 400000,
                        'fee' => 2800,
                        'currency' => 'IDR',
                        'paid_at' => now()->subDays(12),
                    ]
                );

                CouponRedemption::updateOrCreate(
                    ['order_id' => $order2->id],
                    [
                        'coupon_id' => $coupon1->id,
                        'user_id' => $studentBudi->id,
                        'discount_amount' => 100000,
                        'redeemed_at' => now()->subDays(12),
                    ]
                );
            }

            // Order 3: Pending Order
            $order3 = Order::updateOrCreate(
                ['number' => 'ORD-20260927-PND03'],
                [
                    'uuid' => (string) Str::uuid(),
                    'user_id' => $studentBudi->id,
                    'status' => OrderStatus::Pending,
                    'currency' => 'IDR',
                    'subtotal' => 499000,
                    'discount_total' => 0,
                    'tax_total' => 0,
                    'total' => 499000,
                    'expires_at' => now()->addHours(18),
                ]
            );

            OrderItem::updateOrCreate(
                ['order_id' => $order3->id, 'purchasable_id' => $planMonthly->id],
                [
                    'purchasable_type' => 'plan',
                    'name' => 'Pro Trader Bulanan',
                    'unit_price' => 499000,
                    'quantity' => 1,
                    'discount' => 0,
                    'total' => 499000,
                ]
            );

            Payment::updateOrCreate(
                ['order_id' => $order3->id],
                [
                    'uuid' => (string) Str::uuid(),
                    'gateway' => PaymentGateway::Midtrans,
                    'gateway_ref' => 'MID-VA-20260927-11223',
                    'method' => 'Mandiri Virtual Account',
                    'status' => PaymentStatus::Pending,
                    'amount' => 499000,
                    'fee' => 4400,
                    'currency' => 'IDR',
                    'expires_at' => now()->addHours(18),
                ]
            );
        }

        // Payout to Alex
        Payout::updateOrCreate(
            ['reference' => 'TRF-BCA-20260920-ALX'],
            [
                'affiliate_id' => $affiliateAlex->id,
                'amount' => 1000000,
                'method' => 'bank_transfer',
                'status' => 'paid',
                'paid_at' => now()->subDays(7),
                'note' => 'Pencairan komisi periode 1 September',
            ]
        );

        $affiliateAlex->recalculateTotals();
    }
}
