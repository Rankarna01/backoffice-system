<?php

namespace Database\Seeders;

use App\Domain\Analytics\Models\AnalyticsDailySnapshot;
use App\Domain\Analytics\Models\PlatformActivityEvent;
use App\Models\User;
use Illuminate\Database\Seeder;

class AnalyticsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::take(10)->get();

        // Seed 14 daily snapshots
        for ($i = 13; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();

            $newOrders = rand(3, 12);
            $paidOrders = rand(2, $newOrders);
            $grossRevenue = $paidOrders * rand(450000, 1500000);
            $netRevenue = $grossRevenue * 0.95; // after 5% gateway/affiliate fees

            AnalyticsDailySnapshot::updateOrCreate(
                ['snapshot_date' => $date],
                [
                    'gross_revenue' => $grossRevenue,
                    'net_revenue' => $netRevenue,
                    'new_orders_count' => $newOrders,
                    'paid_orders_count' => $paidOrders,
                    'new_users_count' => rand(5, 25),
                    'active_subscribers_count' => rand(85, 120),
                    'course_enrollments_count' => rand(4, 15),
                    'lesson_completions_count' => rand(25, 95),
                    'quiz_attempts_count' => rand(10, 40),
                    'live_attendees_count' => ($i % 3 === 0) ? rand(45, 130) : 0,
                    'category_revenue_distribution' => [
                        'Forex Fundamentals' => round($grossRevenue * 0.40),
                        'Technical Price Action' => round($grossRevenue * 0.35),
                        'Crypto & Web3 Trading' => round($grossRevenue * 0.15),
                        'Risk Management & Psychology' => round($grossRevenue * 0.10),
                    ],
                    'payment_methods_distribution' => [
                        'midtrans' => round($grossRevenue * 0.60),
                        'manual_bank_transfer' => round($grossRevenue * 0.30),
                        'xendit' => round($grossRevenue * 0.10),
                    ],
                ]
            );
        }

        // Seed some sample events
        $eventNames = ['user.login', 'course.progress', 'signal.viewed', 'calculator.risk_reward'];
        foreach ($users as $user) {
            foreach ($eventNames as $eventName) {
                PlatformActivityEvent::create([
                    'user_id' => $user->id,
                    'event_name' => $eventName,
                    'entity_type' => 'Course',
                    'entity_id' => 1,
                    'payload' => ['browser' => 'Chrome', 'device' => 'Desktop'],
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)',
                    'created_at' => now()->subHours(rand(1, 72)),
                ]);
            }
        }
    }
}
