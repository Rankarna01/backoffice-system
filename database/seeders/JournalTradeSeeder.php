<?php

namespace Database\Seeders;

use App\Domain\Trading\Models\JournalTrade;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class JournalTradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        if ($users->isEmpty()) {
            return;
        }

        // Seed realistic trades for each user (so both mentor and customer have their own journals)
        foreach ($users as $user) {
            $sampleTrades = [
                [
                    'uuid' => (string) Str::uuid(),
                    'user_id' => $user->id,
                    'entry_time' => Carbon::now()->subDays(1)->setTime(14, 30),
                    'pair' => 'XAU/USD',
                    'type' => 'long',
                    'status' => 'win',
                    'currency' => 'USD',
                    'risk_amount' => 150.00,
                    'profit_loss' => 450.00,
                    'setup' => 'Breakout SnR + FVG London',
                    'emotion' => 'Tenang & Disiplin',
                    'session' => 'Sesi London (Eropa)',
                    'is_disciplined' => true,
                    'notes' => 'Konfirmasi reject di 2380.00 dengan candle engulfing M15. TP hit mulus di 2395.00.',
                ],
                [
                    'uuid' => (string) Str::uuid(),
                    'user_id' => $user->id,
                    'entry_time' => Carbon::now()->subDays(2)->setTime(20, 15),
                    'pair' => 'BTC/USDT',
                    'type' => 'long',
                    'status' => 'open',
                    'currency' => 'USD',
                    'risk_amount' => 200.00,
                    'profit_loss' => 180.00,
                    'setup' => 'Trend Continuation H4',
                    'emotion' => 'Percaya Diri',
                    'session' => 'Sesi New York (US)',
                    'is_disciplined' => true,
                    'notes' => 'Entry setelah pullback di MA 50 H4, trailing stop sudah dipasang.',
                ],
                [
                    'uuid' => (string) Str::uuid(),
                    'user_id' => $user->id,
                    'entry_time' => Carbon::now()->subDays(3)->setTime(9, 45),
                    'pair' => 'EUR/USD',
                    'type' => 'short',
                    'status' => 'loss',
                    'currency' => 'USD',
                    'risk_amount' => 100.00,
                    'profit_loss' => -100.00,
                    'setup' => 'Liquidity Sweep Asia',
                    'emotion' => 'Ragu-ragu / Takut',
                    'session' => 'Sesi Asia (Tokyo)',
                    'is_disciplined' => false,
                    'notes' => 'Terlalu terburu-buru entry sebelum ada konfirmasi BOS. SL kena persis sebelum harga turun.',
                ],
            ];

            foreach ($sampleTrades as $trade) {
                JournalTrade::updateOrCreate(
                    [
                        'user_id' => $trade['user_id'],
                        'pair' => $trade['pair'],
                        'entry_time' => $trade['entry_time'],
                    ],
                    $trade
                );
            }
        }
    }
}
