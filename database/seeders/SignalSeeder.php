<?php

namespace Database\Seeders;

use App\Domain\Market\Models\Signal;
use App\Models\User;
use Illuminate\Database\Seeder;

class SignalSeeder extends Seeder
{
    /**
     * Run the database seeds for Trading Signals across multiple market categories.
     */
    public function run(): void
    {
        $mentor = User::where('email', 'alex@tradingedu.com')->first()
            ?: User::where('email', 'admin@tradingedu.com')->first();
        $mentorId = $mentor?->id;

        $signals = [
            // -------------------------------------------------------------
            // 1. KOMODITAS & LOGAM (Gold XAUUSD, Oil USOIL)
            // -------------------------------------------------------------
            [
                'title' => 'XAUUSD Institutional Demand Sweep & FVG Fill',
                'market_type' => 'commodities',
                'pair' => 'XAUUSD',
                'timeframe' => 'M15',
                'action' => 'BUY',
                'entry_price' => 2642.50,
                'stop_loss' => 2635.00,
                'take_profit_1' => 2665.00,
                'take_profit_2' => 2680.00,
                'take_profit_3' => 2700.00,
                'risk_reward_ratio' => '1:3.0',
                'status' => 'active',
                'result_pips' => null,
                'risk_percentage' => 1.0,
                'analysis_notes' => "Setup sniper buy pada sesi New York:\n1. Terjadi liquidity sweep pada level low London session\n2. Reaksi tegas dengan body candle bullish engulfing di M15\n3. POI mitigasi Fair Value Gap (FVG) area $2642.50.",
                'chart_image_url' => 'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=800&auto=format&fit=crop&q=80',
                'is_premium' => true,
                'published_at' => now()->subHours(2),
            ],
            [
                'title' => 'XAUUSD Asian Range High Liquidity Grab Reversal',
                'market_type' => 'commodities',
                'pair' => 'XAUUSD',
                'timeframe' => 'H1',
                'action' => 'SELL',
                'entry_price' => 2658.00,
                'stop_loss' => 2664.00,
                'take_profit_1' => 2640.00,
                'take_profit_2' => 2625.00,
                'take_profit_3' => 2605.00,
                'risk_reward_ratio' => '1:3.0',
                'status' => 'hit_tp1',
                'result_pips' => 180.0,
                'risk_percentage' => 1.0,
                'analysis_notes' => "Rejection pada supply zone H4 setelah menyapu Buy-Side Liquidity (BSL) sesi Asia.",
                'chart_image_url' => 'https://images.unsplash.com/photo-1642543492481-44e81e3914a7?w=800&auto=format&fit=crop&q=80',
                'is_premium' => true,
                'published_at' => now()->subDays(1),
                'closed_at' => now()->subHours(12),
            ],
            [
                'title' => 'USOIL Daily Key Level Support Retest',
                'market_type' => 'commodities',
                'pair' => 'USOIL',
                'timeframe' => 'H4',
                'action' => 'BUY',
                'entry_price' => 70.20,
                'stop_loss' => 68.90,
                'take_profit_1' => 74.10,
                'take_profit_2' => 76.50,
                'risk_reward_ratio' => '1:3.0',
                'status' => 'active',
                'result_pips' => null,
                'risk_percentage' => 1.0,
                'analysis_notes' => 'Minyak WTI menguji support psikologis $70.00 dengan divergence RSI positif pada H4.',
                'chart_image_url' => 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?w=800&auto=format&fit=crop&q=80',
                'is_premium' => false,
                'published_at' => now()->subHours(5),
            ],

            // -------------------------------------------------------------
            // 2. FOREX CURRENCIES (EURUSD, GBPUSD, USDJPY)
            // -------------------------------------------------------------
            [
                'title' => 'EURUSD Discount POI Mitigation & Trend Continuation',
                'market_type' => 'forex',
                'pair' => 'EURUSD',
                'timeframe' => 'H1',
                'action' => 'BUY',
                'entry_price' => 1.08400,
                'stop_loss' => 1.08150,
                'take_profit_1' => 1.09150,
                'take_profit_2' => 1.09650,
                'take_profit_3' => 1.10200,
                'risk_reward_ratio' => '1:3.0',
                'status' => 'active',
                'result_pips' => null,
                'risk_percentage' => 1.0,
                'analysis_notes' => 'Struktur pasar bullish H4 valid. Harga masuk ke zona discount (0.618 Fib) dan termitigasi pada bullish order block M15.',
                'chart_image_url' => 'https://images.unsplash.com/photo-1590283603385-17ffb3a7f29f?w=800&auto=format&fit=crop&q=80',
                'is_premium' => true,
                'published_at' => now()->subHours(3),
            ],
            [
                'title' => 'GBPUSD Bearish Supply Rejection on London Open',
                'market_type' => 'forex',
                'pair' => 'GBPUSD',
                'timeframe' => 'M15',
                'action' => 'SELL',
                'entry_price' => 1.30500,
                'stop_loss' => 1.30750,
                'take_profit_1' => 1.29750,
                'take_profit_2' => 1.29250,
                'risk_reward_ratio' => '1:3.0',
                'status' => 'hit_tp1',
                'result_pips' => 75.0,
                'risk_percentage' => 1.0,
                'analysis_notes' => 'Break of Structure (BOS) M15 ke bawah dengan momentum kencang pasca data CPI Inggris.',
                'chart_image_url' => 'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=800&auto=format&fit=crop&q=80',
                'is_premium' => true,
                'published_at' => now()->subDays(2),
                'closed_at' => now()->subDays(1),
            ],
            [
                'title' => 'USDJPY Equal Lows Liquidity Sweep & Strong Rebound',
                'market_type' => 'forex',
                'pair' => 'USDJPY',
                'timeframe' => 'H1',
                'action' => 'BUY',
                'entry_price' => 148.200,
                'stop_loss' => 147.800,
                'take_profit_1' => 149.400,
                'risk_reward_ratio' => '1:3.0',
                'status' => 'closed',
                'result_pips' => 120.0,
                'risk_percentage' => 1.0,
                'analysis_notes' => 'Posisi ditutup manual setelah menyentuh level resistance dinamis dan pidato Gubernur BOJ.',
                'chart_image_url' => null,
                'is_premium' => false,
                'published_at' => now()->subDays(3),
                'closed_at' => now()->subDays(2),
            ],

            // -------------------------------------------------------------
            // 3. KRIPTO DERIVATIF (BTCUSDT, ETHUSDT, SOLUSDT)
            // -------------------------------------------------------------
            [
                'title' => 'BTCUSDT Macro Range Low Liquidity Absorption',
                'market_type' => 'crypto',
                'pair' => 'BTCUSDT',
                'timeframe' => 'H4',
                'action' => 'BUY',
                'entry_price' => 63500.00,
                'stop_loss' => 61800.00,
                'take_profit_1' => 68600.00,
                'take_profit_2' => 72000.00,
                'risk_reward_ratio' => '1:3.0',
                'status' => 'active',
                'result_pips' => null,
                'risk_percentage' => 1.5,
                'analysis_notes' => "Akumulasi institusional di bawah $64k. Open Interest naik disertai funding rate netral yang mendukung breakout ke $70k.",
                'chart_image_url' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=800&auto=format&fit=crop&q=80',
                'is_premium' => true,
                'published_at' => now()->subHours(8),
            ],
            [
                'title' => 'ETHUSDT Support Flip with High Volume Confirmation',
                'market_type' => 'crypto',
                'pair' => 'ETHUSDT',
                'timeframe' => 'H1',
                'action' => 'BUY',
                'entry_price' => 2640.00,
                'stop_loss' => 2580.00,
                'take_profit_1' => 2820.00,
                'risk_reward_ratio' => '1:3.0',
                'status' => 'hit_tp1',
                'result_pips' => 180.0,
                'risk_percentage' => 1.0,
                'analysis_notes' => 'S/R flip yang bersih pada level $2.640 dengan lonjakan volume spot taker buy.',
                'chart_image_url' => 'https://images.unsplash.com/photo-1622979135225-d2ba269bc1df?w=800&auto=format&fit=crop&q=80',
                'is_premium' => true,
                'published_at' => now()->subDays(2),
                'closed_at' => now()->subDays(1),
            ],
            [
                'title' => 'SOLUSDT Overextended Momentum & Double Top Sweep',
                'market_type' => 'crypto',
                'pair' => 'SOLUSDT',
                'timeframe' => 'H1',
                'action' => 'SELL',
                'entry_price' => 156.00,
                'stop_loss' => 160.00,
                'take_profit_1' => 144.00,
                'risk_reward_ratio' => '1:3.0',
                'status' => 'hit_sl',
                'result_pips' => -40.0,
                'risk_percentage' => 1.0,
                'analysis_notes' => 'Posisi terkena stop loss akibat sentimen berita ekosistem yang mendorong harga menembus resistance $160.',
                'chart_image_url' => null,
                'is_premium' => false,
                'published_at' => now()->subDays(4),
                'closed_at' => now()->subDays(3),
            ],

            // -------------------------------------------------------------
            // 4. INDEKS SAHAM GLOBAL (US30 Dow, NAS100 Nasdaq)
            // -------------------------------------------------------------
            [
                'title' => 'US30 NY Session Open Breakout & Pullback Play',
                'market_type' => 'indices',
                'pair' => 'US30',
                'timeframe' => 'M15',
                'action' => 'BUY',
                'entry_price' => 42100.00,
                'stop_loss' => 41950.00,
                'take_profit_1' => 42550.00,
                'take_profit_2' => 42800.00,
                'risk_reward_ratio' => '1:3.0',
                'status' => 'active',
                'result_pips' => null,
                'risk_percentage' => 1.0,
                'analysis_notes' => 'Pembukaan market New York dengan volume institusi kuat menembus all-time high resistance.',
                'chart_image_url' => 'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=800&auto=format&fit=crop&q=80',
                'is_premium' => true,
                'published_at' => now()->subHours(1),
            ],
            [
                'title' => 'NAS100 Tech Rally Continuation after Fed Rate Decision',
                'market_type' => 'indices',
                'pair' => 'NAS100',
                'timeframe' => 'H1',
                'action' => 'BUY',
                'entry_price' => 20150.00,
                'stop_loss' => 20020.00,
                'take_profit_1' => 20540.00,
                'risk_reward_ratio' => '1:3.0',
                'status' => 'hit_tp1',
                'result_pips' => 390.0,
                'risk_percentage' => 1.0,
                'analysis_notes' => 'Laporan laba sektor semikonduktor memicu kelanjutan tren kenaikan Nasdaq.',
                'chart_image_url' => 'https://images.unsplash.com/photo-1590283603385-17ffb3a7f29f?w=800&auto=format&fit=crop&q=80',
                'is_premium' => true,
                'published_at' => now()->subDays(3),
                'closed_at' => now()->subDays(2),
            ],
        ];

        foreach ($signals as $s) {
            Signal::updateOrCreate(
                [
                    'pair' => $s['pair'],
                    'title' => $s['title'],
                ],
                array_merge($s, [
                    'mentor_id' => $mentorId,
                ])
            );
        }
    }
}
