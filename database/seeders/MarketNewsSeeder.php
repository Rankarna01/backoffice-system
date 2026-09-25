<?php

namespace Database\Seeders;

use App\Domain\Market\Models\MarketNews;
use App\Models\User;
use Illuminate\Database\Seeder;

class MarketNewsSeeder extends Seeder
{
    /**
     * Run the database seeds for Market News.
     */
    public function run(): void
    {
        $mentor = User::where('email', 'alex@tradingedu.com')->first()
            ?: User::where('email', 'admin@tradingedu.com')->first();
        $authorId = $mentor?->id;

        $news = [
            [
                'title' => 'The Fed Pangkas Suku Bunga Acuan 50 Bps, Beri Sinyal Keyakinan Soft Landing Ekonomi AS',
                'slug' => 'the-fed-pangkas-suku-bunga-acuan-50-bps-beri-sinyal-soft-landing',
                'summary' => 'Federal Reserve resmi memulai siklus pelonggaran kebijakan moneter dengan memangkas suku bunga acuan sebesar 50 basis poin ke kisaran 4,75% - 5,00%, memicu lonjakan volatilitas di seluruh pasar keuangan.',
                'content' => '<p>Komite Pasar Terbuka Federal (FOMC) memutuskan untuk memangkas suku bunga sebesar setengah poin persentase, sebuah langkah agresif yang jarang dilakukan di luar masa krisis ekonomi.</p><h3>Pernyataan Jerome Powell</h3><p>Ketua The Fed Jerome Powell menegaskan bahwa pemangkasan besar ini bertujuan untuk menjaga kekuatan pasar tenaga kerja AS yang mulai menunjukkan perlambatan, seraya memastikan inflasi terus bergerak menuju target 2%.</p><h3>Reaksi Pasar Keuangan</h3><p>Dolar AS (DXY) langsung anjlok menyentuh level terendah dalam 14 bulan, sementara indeks saham Wall Street dan harga emas mencatatkan reli penguatan signifikan.</p>',
                'category' => 'central_banks',
                'impact_level' => 'high',
                'sentiment' => 'bullish',
                'source' => 'Bloomberg',
                'source_url' => 'https://www.bloomberg.com',
                'cover_image_url' => 'https://images.unsplash.com/photo-1590283603385-17ffb3a7f29f?w=800&auto=format&fit=crop&q=80',
                'related_symbols' => ['DXY', 'EURUSD', 'XAUUSD', 'US30'],
                'is_breaking' => true,
                'is_featured' => true,
                'status' => 'published',
                'views_count' => 3420,
                'published_at' => now()->subHours(4),
            ],
            [
                'title' => 'Emas Cetak Rekor Tertinggi Sepanjang Masa di $2.685 Didorong Ketegangan Geopolitik Timur Tengah',
                'slug' => 'emas-cetak-rekor-tertinggi-sepanjang-masa-2685-geopolitik',
                'summary' => 'Permintaan lindung nilai (safe-haven demand) mendongkrak harga emas batangan mendekati batas psikologis $2.700 per troy ounce seiring eskalasi konflik lintas batas di kawasan Timur Tengah.',
                'content' => '<p>Harga emas di pasar spot internasional terus mencatatkan reli tanpa henti. Ketidakpastian geopolitik yang kian membara membuat bank sentral global dan dana institusi terus menambah cadangan emas fisik.</p><p>Analis komoditas memperkirakan momentum bullish masih berpeluang bertahan selama harga emas mampu bertahan di atas zona support $2.630.</p>',
                'category' => 'commodities',
                'impact_level' => 'high',
                'sentiment' => 'bullish',
                'source' => 'Reuters',
                'source_url' => 'https://www.reuters.com',
                'cover_image_url' => 'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=800&auto=format&fit=crop&q=80',
                'related_symbols' => ['XAUUSD', 'XAGUSD', 'USOIL'],
                'is_breaking' => false,
                'is_featured' => true,
                'status' => 'published',
                'views_count' => 2890,
                'published_at' => now()->subHours(8),
            ],
            [
                'title' => 'Inflasi Zona Euro Melambat ke 1,8%, ECB Berpeluang Percepat Pemangkasan Suku Bunga Oktober',
                'slug' => 'inflasi-zona-euro-melambat-ke-18-ecb-percepat-pemangkasan',
                'summary' => 'Data inflasi tahunan Zona Euro untuk pertama kalinya dalam 3 tahun turun di bawah target 2% ECB, membuka pintu lebar bagi pemangkasan suku bunga lanjutan pada pertemuan dewan gubernur mendatang.',
                'content' => '<p>Eurostat melaporkan inflasi indeks harga konsumen melambat ke 1,8% YoY, didorong oleh penurunan tajam harga energi. EUR/USD merespons data ini dengan bergerak melemah tipis terhadap mata uang utama lainnya.</p>',
                'category' => 'forex',
                'impact_level' => 'medium',
                'sentiment' => 'bearish',
                'source' => 'Financial Times',
                'source_url' => 'https://www.ft.com',
                'cover_image_url' => 'https://images.unsplash.com/photo-1526304640581-d334cdbbf45e?w=800&auto=format&fit=crop&q=80',
                'related_symbols' => ['EURUSD', 'EURGBP', 'EURJPY'],
                'is_breaking' => false,
                'is_featured' => false,
                'status' => 'published',
                'views_count' => 1450,
                'published_at' => now()->subDays(1),
            ],
            [
                'title' => 'SEC Berikan Lampu Hijau Perdagangan Opsi ETF Spot Bitcoin di Bursa Nasdaq',
                'slug' => 'sec-berikan-lampu-hijau-perdagangan-opsi-etf-spot-bitcoin-nasdaq',
                'summary' => 'Komisi Sekuritas dan Bursa AS (SEC) menyetujui peluncuran kontrak opsi untuk produk ETF Bitcoin milik BlackRock (IBIT), menandai tonggak sejarah baru dalam likuiditas institusional kripto.',
                'content' => '<p>Persetujuan opsi ETF ini diperkirakan akan menarik gelombang likuiditas derivatif masif dari hedge fund dan pengelola dana pensiun tradisional, memungkinkan strategi hedging portofolio kripto yang lebih fleksibel dan teregulasi.</p>',
                'category' => 'crypto',
                'impact_level' => 'high',
                'sentiment' => 'bullish',
                'source' => 'CoinDesk',
                'source_url' => 'https://www.coindesk.com',
                'cover_image_url' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=800&auto=format&fit=crop&q=80',
                'related_symbols' => ['BTCUSDT', 'ETHUSDT', 'SOLUSDT'],
                'is_breaking' => true,
                'is_featured' => true,
                'status' => 'published',
                'views_count' => 4120,
                'published_at' => now()->subDays(2),
            ],
            [
                'title' => 'Bank Sentral China (PBOC) Gelontorkan Paket Stimulus Moneter Raksasa untuk Dorong Pertumbuhan',
                'slug' => 'bank-sentral-china-pboc-gelontorkan-stimulus-moneter-raksasa',
                'summary' => 'PBOC memangkas rasio cadangan wajib perbankan (RRR) sebesar 50 bps dan suku bunga reverse repo guna menyuntikkan likuiditas sekitar 1 triliun yuan ke dalam sistem finansial.',
                'content' => '<p>Paket stimulus komprehensif ini mencakup dukungan likuiditas untuk pasar saham, penurunan suku bunga hipotek properti, dan pelonggaran likuiditas perbankan nasional, yang seketika mengangkat harga komoditas global dan mata uang berbasis ekspor seperti Dolar Australia (AUD).</p>',
                'category' => 'macro_economy',
                'impact_level' => 'high',
                'sentiment' => 'bullish',
                'source' => 'Caixin Global',
                'source_url' => 'https://www.caixinglobal.com',
                'cover_image_url' => 'https://images.unsplash.com/photo-1642543492481-44e81e3914a7?w=800&auto=format&fit=crop&q=80',
                'related_symbols' => ['AUDUSD', 'USOIL', 'XAUUSD', 'HANGSENG'],
                'is_breaking' => false,
                'is_featured' => false,
                'status' => 'published',
                'views_count' => 1980,
                'published_at' => now()->subDays(3),
            ],
        ];

        foreach ($news as $item) {
            MarketNews::updateOrCreate(
                ['slug' => $item['slug']],
                array_merge($item, [
                    'author_id' => $authorId,
                ])
            );
        }
    }
}
