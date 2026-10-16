<?php

namespace Database\Seeders;

use App\Domain\Trading\Models\LiveChartConfig;
use App\Domain\Trading\Models\PaperTradingAccount;
use App\Domain\Trading\Models\PaperTradingPosition;
use App\Models\User;
use Illuminate\Database\Seeder;

class PaperTradingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Global Live Chart Config
        LiveChartConfig::updateOrCreate(
            ['customer_id' => null],
            [
                'default_symbol' => 'OANDA:XAUUSD',
                'default_interval' => '60',
                'theme' => 'dark',
                'locale' => 'id',
                'timezone' => 'Asia/Jakarta',
                'watchlist' => LiveChartConfig::getDefaultWatchlist(),
                'allow_trading' => true,
                'default_balance' => 10000.00,
                'is_active' => true,
            ]
        );

        // 2. Seed Accounts for users
        $users = User::take(5)->get();

        foreach ($users as $index => $user) {
            $account = PaperTradingAccount::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'account_number' => 'SIM-' . (88200 + $index),
                    'balance' => 10000.00,
                    'equity' => 10000.00,
                    'currency' => 'USD',
                    'initial_deposit' => 10000.00,
                    'status' => 'active',
                    'notes' => 'Akun simulasi trading live default $10,000.',
                ]
            );

            // Sample positions for the first user
            if ($index === 0 && $account->positions()->count() === 0) {
                // Open position: XAU/USD BUY
                PaperTradingPosition::create([
                    'paper_trading_account_id' => $account->id,
                    'user_id' => $user->id,
                    'symbol' => 'XAU/USD',
                    'side' => 'buy',
                    'units' => 1.0000,
                    'entry_price' => 4185.00,
                    'current_price' => 4187.01,
                    'stop_loss' => 4160.00,
                    'take_profit' => 4220.00,
                    'profit_loss' => 2.01,
                    'status' => 'open',
                    'opened_at' => now()->subMinutes(45),
                ]);

                // Closed trade: BTC/USDT BUY
                PaperTradingPosition::create([
                    'paper_trading_account_id' => $account->id,
                    'user_id' => $user->id,
                    'symbol' => 'BTC/USDT',
                    'side' => 'buy',
                    'units' => 0.1000,
                    'entry_price' => 81200.00,
                    'current_price' => 81997.98,
                    'close_price' => 81950.00,
                    'stop_loss' => 80500.00,
                    'take_profit' => 82000.00,
                    'profit_loss' => 75.00,
                    'status' => 'closed',
                    'opened_at' => now()->subHours(6),
                    'closed_at' => now()->subHours(2),
                    'close_reason' => 'take_profit',
                ]);

                $account->recalculateEquity();
            }
        }
    }
}
