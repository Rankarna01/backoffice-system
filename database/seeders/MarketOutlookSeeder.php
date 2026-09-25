<?php

namespace Database\Seeders;

use App\Domain\Market\Models\MarketOutlook;
use App\Models\User;
use Illuminate\Database\Seeder;

class MarketOutlookSeeder extends Seeder
{
    /**
     * Run the database seeds for Market Outlooks.
     */
    public function run(): void
    {
        $mentor = User::where('email', 'alex@tradingedu.com')->first()
            ?: User::where('email', 'admin@tradingedu.com')->first();
        $mentorId = $mentor?->id;

        $outlooks = [
            [
                'title' => 'Weekly Outlook: Emas (XAUUSD) Mengincar All-Time High $2.700 di Tengah Volatilitas Geopolitik',
                'slug' => 'weekly-outlook-emas-xauusd-mengincar-all-time-high-2700',
                'summary' => 'Harga emas spot global mempertahankan bias bullish kuat setelah berhasil mempertahankan area support institusional $2.630. Katalis pelemahan imbal hasil obligasi AS dan ketegangan Timur Tengah memicu akumulasi safe-haven baru.',
                'content' => '<h2>Latar Belakang Fundamental & Makro</h2><p>Pekan ini pasar komoditas kembali diwarnai oleh arus masuk likuiditas yang signifikan ke instrumen safe haven. Pernyataan dovish dari pejabat Federal Reserve kian mempertegas siklus pelonggaran moneter lanjutan hingga akhir tahun.</p><h3>Analisis Struktur Teknikal XAU/USD</h3><p>Pada time frame H4 dan Daily, harga emas membentuk struktur Higher High dan Higher Low yang sangat rapi. Penembusan area $2.650 mengonfirmasi pembentukan Break of Structure (BOS) baru menuju proyeksi ekstensi Fibonacci 1.618 pada area $2.700.</p><h3>Katalis Penggerak Sepekan ke Depan</h3><ul><li>Rilis data Core PCE Price Index AS</li><li>Pidato Ketua Federal Reserve Jerome Powell</li><li>Data Klaim Pengangguran Mingguan AS</li></ul>',
                'sentiment' => 'bullish',
                'market_category' => 'commodities',
                'featured_pairs' => ['XAUUSD', 'DXY', 'USOIL', 'US10Y'],
                'time_horizon' => 'weekly',
                'cover_image_url' => 'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=800&auto=format&fit=crop&q=80',
                'key_takeaways' => [
                    'Emas mempertahankan support kuat pada area $2.630 - $2.635',
                    'Pelemahan Indeks Dolar (DXY) di bawah 101.00 membuka akselerasi tren naik baru',
                    'Resistensi psikologis kunci berikutnya berada di level $2.685 dan $2.700',
                    'Waspadai aksi profit taking jangka pendek menjelang rilis data inflasi AS',
                ],
                'support_resistance_levels' => [
                    [
                        'pair' => 'XAUUSD',
                        'support' => '$2,630 - $2,635',
                        'resistance' => '$2,685 - $2,700',
                        'bias' => 'BULLISH',
                    ],
                    [
                        'pair' => 'DXY',
                        'support' => '100.20',
                        'resistance' => '101.80',
                        'bias' => 'BEARISH',
                    ],
                    [
                        'pair' => 'USOIL',
                        'support' => '$68.50',
                        'resistance' => '$74.00',
                        'bias' => 'BULLISH',
                    ],
                ],
                'is_premium' => true,
                'status' => 'published',
                'views_count' => 1240,
                'published_at' => now()->subDays(1),
            ],
            [
                'title' => 'Forex Macro: Divergensi Kebijakan Moneter Fed vs ECB dan Dampaknya pada EUR/USD',
                'slug' => 'forex-macro-divergensi-kebijakan-moneter-fed-vs-ecb-dan-eurusd',
                'summary' => 'Divergensi arah pertumbuhan ekonomi Amerika Serikat dan Zona Euro mulai menunjukkan polarisasi yang berdampak langsung pada pergerakan EUR/USD dan GBP/USD.',
                'content' => '<h2>Dilema Pertumbuhan Ekonomi Eropa</h2><p>Data PMI Manufaktur Jerman dan Prancis masih mencerminkan kontraksi di bawah angka 50, meningkatkan ekspektasi bahwa Bank Sentral Eropa (ECB) mungkin harus memangkas suku bunga lebih agresif daripada perkiraan awal pasar.</p><h3>Peta Pergerakan EUR/USD</h3><p>Pasangan mata uang EUR/USD saat ini terjebak dalam fase sideways antara batas bawah 1.0800 dan batas atas 1.0950. Diperlukan penembusan tegas dari salah satu level batas tersebut untuk menentukan arah tren kuartal keempat.</p>',
                'sentiment' => 'neutral',
                'market_category' => 'forex',
                'featured_pairs' => ['EURUSD', 'GBPUSD', 'USDJPY'],
                'time_horizon' => 'weekly',
                'cover_image_url' => 'https://images.unsplash.com/photo-1590283603385-17ffb3a7f29f?w=800&auto=format&fit=crop&q=80',
                'key_takeaways' => [
                    'Ekspektasi pemangkasan suku bunga Fed sebesar 50 bps telah terdiskon sebagian oleh pasar',
                    'EUR/USD tertahan dalam rentang konsolidasi 1.0800 - 1.0950',
                    'Yen Jepang (USD/JPY) berpotensi volatile menyusul pemilihan kepemimpinan politik di Tokyo',
                ],
                'support_resistance_levels' => [
                    [
                        'pair' => 'EURUSD',
                        'support' => '1.0800 - 1.0820',
                        'resistance' => '1.0950 - 1.0980',
                        'bias' => 'NEUTRAL',
                    ],
                    [
                        'pair' => 'GBPUSD',
                        'support' => '1.3000',
                        'resistance' => '1.3150',
                        'bias' => 'NEUTRAL',
                    ],
                    [
                        'pair' => 'USDJPY',
                        'support' => '146.50',
                        'resistance' => '149.80',
                        'bias' => 'BULLISH',
                    ],
                ],
                'is_premium' => false,
                'status' => 'published',
                'views_count' => 850,
                'published_at' => now()->subDays(2),
            ],
            [
                'title' => 'Crypto Outlook: Siklus Likuiditas Kuartal 4 dan Target Breakout Bitcoin Menuju $70.000',
                'slug' => 'crypto-outlook-siklus-likuiditas-kuartal-4-dan-target-breakout-bitcoin-70000',
                'summary' => 'Secara historis, Kuartal 4 (Uptober) kerap menjadi periode dengan performa bulanan terbaik bagi Bitcoin dan aset kripto. Metrik on-chain dan arus masuk ETF spot mencatatkan titik balik positif.',
                'content' => '<h2>Akumulasi Institutional On-Chain</h2><p>Data Glassnode menunjukkan saldo Bitcoin di bursa terpusat (exchanges) terus menyusut ke level terendah dalam 5 tahun terakhir, mengindikasikan adanya tekanan beli jangka panjang dari entitas whale dan institusi ETF.</p><h3>Struktur Breakout $65.000</h3><p>Penembusan level psikologis $65.000 dengan volume spot yang sehat membuka jalan bagi pengujian kembali area resistensi kritis $68.000 sebelum menuju rekor tertinggi sepanjang masa baru.</p>',
                'sentiment' => 'bullish',
                'market_category' => 'crypto',
                'featured_pairs' => ['BTCUSDT', 'ETHUSDT', 'SOLUSDT'],
                'time_horizon' => 'monthly',
                'cover_image_url' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=800&auto=format&fit=crop&q=80',
                'key_takeaways' => [
                    'Arus masuk bersih Bitcoin ETF Spot mencatatkan angka positif 3 pekan beruntun',
                    'Open interest di pasar derivatif mengalami ekspansi sehat tanpa tanda-tanda over-leverage',
                    'Altcoin utama seperti Ethereum dan Solana menunjukkan sinyal akumulasi di area support penting',
                ],
                'support_resistance_levels' => [
                    [
                        'pair' => 'BTCUSDT',
                        'support' => '$62,500',
                        'resistance' => '$68,500 - $70,000',
                        'bias' => 'BULLISH',
                    ],
                    [
                        'pair' => 'ETHUSDT',
                        'support' => '$2,500',
                        'resistance' => '$2,850',
                        'bias' => 'BULLISH',
                    ],
                ],
                'is_premium' => true,
                'status' => 'published',
                'views_count' => 1580,
                'published_at' => now()->subDays(3),
            ],
            [
                'title' => 'Indeks Saham Wall Street: Waspadai Volatilitas Menjelang Pemilu AS dan Rotasi Sektor',
                'slug' => 'indeks-saham-wall-street-waspadai-volatilitas-menjelang-pemilu-as',
                'summary' => 'Indeks S&P 500 dan Dow Jones mencatatkan rekor tertinggi baru, namun indikator breadth pasar memperingatkan potensi koreksi jangka pendek seiring meningkatnya indeks ketakutan pasar VIX.',
                'content' => '<h2>Rotasi Menuju Saham Nilai & Defensif</h2><p>Investor institusional mulai melakukan lindung nilai (hedging) dengan mengalihkan porsi portofolio dari saham teknologi megacap ke sektor utilitas, kesehatan, dan dividen stabil menjelang hajatan pemilu presiden AS.</p>',
                'sentiment' => 'volatile',
                'market_category' => 'indices',
                'featured_pairs' => ['US30', 'NAS100', 'SPX500'],
                'time_horizon' => 'weekly',
                'cover_image_url' => 'https://images.unsplash.com/photo-1642543492481-44e81e3914a7?w=800&auto=format&fit=crop&q=80',
                'key_takeaways' => [
                    'Indeks VIX mulai merangkak naik mendekati level 20',
                    'Valuasi indeks Nasdaq mulai tertekan oleh kenaikan yield obligasi jangka panjang',
                    'Pendekatan defensif dengan batas risiko ketat disarankan bagi trader indeks intraday',
                ],
                'support_resistance_levels' => [
                    [
                        'pair' => 'US30',
                        'support' => '41,800',
                        'resistance' => '42,600',
                        'bias' => 'NEUTRAL',
                    ],
                    [
                        'pair' => 'NAS100',
                        'support' => '19,800',
                        'resistance' => '20,400',
                        'bias' => 'VOLATILE',
                    ],
                ],
                'is_premium' => true,
                'status' => 'draft',
                'views_count' => 0,
                'published_at' => now()->addDays(1),
            ],
        ];

        foreach ($outlooks as $data) {
            MarketOutlook::updateOrCreate(
                ['slug' => $data['slug']],
                array_merge($data, [
                    'mentor_id' => $mentorId,
                ])
            );
        }
    }
}
