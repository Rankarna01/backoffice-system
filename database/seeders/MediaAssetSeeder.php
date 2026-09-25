<?php

namespace Database\Seeders;

use App\Domain\Media\Models\MediaAsset;
use App\Models\User;
use App\Services\CloudflareR2Service;
use Illuminate\Database\Seeder;

class MediaAssetSeeder extends Seeder
{
    /**
     * Run the database seeds for Cloudflare R2 Media Assets.
     */
    public function run(): void
    {
        /** @var CloudflareR2Service $r2 */
        $r2 = app(CloudflareR2Service::class);
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $adminId = $admin?->id;

        $assets = [
            // 1. Video Materi Pelajaran (Lessons)
            [
                'name' => 'Video Materi: Mengapa 95% Retail Trader Merugi di Pasar Finansial',
                'file_name' => 'smc-lesson-01-retail-trap.mp4',
                'file_path' => 'lessons/videos/smc-lesson-01-retail-trap.mp4',
                'disk' => 'r2',
                'mime_type' => 'video/mp4',
                'size_bytes' => 194_000_000, // 194 MB
                'type' => 'video',
                'collection' => 'lessons',
                'alt_text' => 'Video pengantar SMC tentang retail traps',
                'description' => 'Video materi resolusi 1080p 60fps dengan bitrate 4500kbps, dioptimasi untuk edge streaming Cloudflare Stream / R2.',
                'metadata' => [
                    'duration_seconds' => 900,
                    'resolution' => '1920x1080',
                    'codec' => 'h264',
                ],
            ],
            [
                'name' => 'Video Materi: Anatomi Candlestick & Siklus Likuiditas Institusi',
                'file_name' => 'smc-lesson-02-liquidity-cycle.mp4',
                'file_path' => 'lessons/videos/smc-lesson-02-liquidity-cycle.mp4',
                'disk' => 'r2',
                'mime_type' => 'video/mp4',
                'size_bytes' => 256_000_000, // 256 MB
                'type' => 'video',
                'collection' => 'lessons',
                'alt_text' => 'Video siklus likuiditas institusi',
                'description' => 'Penjelasan mekanika order flow institusional dan bagaimana smart money mengakumulasi posisi.',
                'metadata' => [
                    'duration_seconds' => 1200,
                    'resolution' => '1920x1080',
                    'codec' => 'h264',
                ],
            ],
            [
                'name' => 'Video Materi: Setup Break of Structure (BOS) Live Backtest',
                'file_name' => 'smc-lesson-03-bos-backtest.mp4',
                'file_path' => 'lessons/videos/smc-lesson-03-bos-backtest.mp4',
                'disk' => 'r2',
                'mime_type' => 'video/mp4',
                'size_bytes' => 430_000_000, // 430 MB
                'type' => 'video',
                'collection' => 'lessons',
                'alt_text' => 'Live backtest studi kasus BOS',
                'description' => 'Rekaman sesi backtesting 50 trade setup BOS EURUSD dan GBPUSD pada time frame M15/M1.',
                'metadata' => [
                    'duration_seconds' => 1800,
                    'resolution' => '1920x1080',
                    'codec' => 'h264',
                ],
            ],
            [
                'name' => 'Video Materi: Kalkulasi Lot & Manajemen Leverage Forex Dasar',
                'file_name' => 'forex-lesson-01-lot-calculation.mp4',
                'file_path' => 'lessons/videos/forex-lesson-01-lot-calculation.mp4',
                'disk' => 'r2',
                'mime_type' => 'video/mp4',
                'size_bytes' => 128_000_000, // 128 MB
                'type' => 'video',
                'collection' => 'lessons',
                'alt_text' => 'Tutorial simulasi lot dan margin trading forex',
                'description' => 'Panduan visual cara menghitung lot size sesuai toleransi risiko 1% modal.',
                'metadata' => [
                    'duration_seconds' => 720,
                    'resolution' => '1920x1080',
                    'codec' => 'h264',
                ],
            ],

            // 2. Cover & Thumbnail Kursus (Courses)
            [
                'name' => 'Cover Kursus: Smart Money Concepts Mastery (Ultra HD)',
                'file_name' => 'smc-mastery-cover.webp',
                'file_path' => 'courses/thumbnails/smc-mastery-cover.webp',
                'disk' => 'r2',
                'mime_type' => 'image/webp',
                'size_bytes' => 880_000, // 880 KB
                'type' => 'image',
                'collection' => 'courses',
                'alt_text' => 'Cover banner kursus Smart Money Concepts Mastery',
                'description' => 'Thumbnail resmi aspek rasio 16:9 format WebP lossy quality 85.',
                'metadata' => [
                    'dimensions' => '1920x1080',
                    'aspect_ratio' => '16:9',
                ],
            ],
            [
                'name' => 'Cover Kursus: Dasar Trading Forex untuk Pemula',
                'file_name' => 'forex-basics-cover.webp',
                'file_path' => 'courses/thumbnails/forex-basics-cover.webp',
                'disk' => 'r2',
                'mime_type' => 'image/webp',
                'size_bytes' => 740_000, // 740 KB
                'type' => 'image',
                'collection' => 'courses',
                'alt_text' => 'Cover banner kursus Forex Dasar Pemula',
                'description' => 'Thumbnail visual mata uang dan candlestick.',
                'metadata' => [
                    'dimensions' => '1920x1080',
                    'aspect_ratio' => '16:9',
                ],
            ],
            [
                'name' => 'Cover Kursus: Gold (XAUUSD) Intraday Trading Playbook',
                'file_name' => 'gold-xauusd-cover.webp',
                'file_path' => 'courses/thumbnails/gold-xauusd-cover.webp',
                'disk' => 'r2',
                'mime_type' => 'image/webp',
                'size_bytes' => 960_000, // 960 KB
                'type' => 'image',
                'collection' => 'courses',
                'alt_text' => 'Cover kursus trading emas XAUUSD',
                'description' => 'Grafis batangan emas dan volatility index chart.',
                'metadata' => [
                    'dimensions' => '1920x1080',
                    'aspect_ratio' => '16:9',
                ],
            ],

            // 3. Foto Profil Mentor & Marketing (Mentors & Marketing)
            [
                'name' => 'Foto Mentor: Alexander Wijaya (Chief Trading Mentor)',
                'file_name' => 'alexander-wijaya-profile.png',
                'file_path' => 'mentors/avatars/alexander-wijaya-profile.png',
                'disk' => 'r2',
                'mime_type' => 'image/png',
                'size_bytes' => 450_000, // 450 KB
                'type' => 'image',
                'collection' => 'mentors',
                'alt_text' => 'Headshot profesional Alexander Wijaya',
                'description' => 'Foto profil mentor dengan latar belakang transparan.',
                'metadata' => [
                    'dimensions' => '800x800',
                    'aspect_ratio' => '1:1',
                ],
            ],
            [
                'name' => 'Banner Promosi: Q4 Trading Masterclass Pro Access',
                'file_name' => 'promo-banner-q4-masterclass.jpg',
                'file_path' => 'marketing/banners/promo-banner-q4-masterclass.jpg',
                'disk' => 'r2',
                'mime_type' => 'image/jpeg',
                'size_bytes' => 1_250_000, // 1.25 MB
                'type' => 'image',
                'collection' => 'marketing',
                'alt_text' => 'Banner promo diskon paket membership',
                'description' => 'Banner promosi marketing untuk landing page dan newsletter.',
                'metadata' => [
                    'dimensions' => '2400x1200',
                ],
            ],

            // 4. Dokumen Silabus, Panduan & Spreadsheet (Modules)
            [
                'name' => 'E-Book Panduan: Mekanisme Likuiditas & Smart Money',
                'file_name' => 'panduan-smc-liquidity.pdf',
                'file_path' => 'modules/documents/panduan-smc-liquidity.pdf',
                'disk' => 'r2',
                'mime_type' => 'application/pdf',
                'size_bytes' => 8_800_000, // 8.8 MB
                'type' => 'document',
                'collection' => 'modules',
                'alt_text' => 'E-Book PDF Panduan SMC',
                'description' => 'Buku panduan digital 42 halaman modul bab 1 kursus SMC.',
                'metadata' => [
                    'pages' => 42,
                    'is_printable' => true,
                ],
            ],
            [
                'name' => 'Slide Presentasi: Market Structure, BOS dan CHoCH',
                'file_name' => 'market-structure-presentation.pptx',
                'file_path' => 'modules/documents/market-structure-presentation.pptx',
                'disk' => 'r2',
                'mime_type' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'size_bytes' => 14_600_000, // 14.6 MB
                'type' => 'document',
                'collection' => 'modules',
                'alt_text' => 'Slide PPTX materi bab 2',
                'description' => 'Slide interaktif PowerPoint berisi diagram dan visualisasi swing high/low.',
                'metadata' => [
                    'slides' => 35,
                ],
            ],
            [
                'name' => 'Template Spreadsheet: Jurnal Trading Otomatis & Risk Calculator',
                'file_name' => 'jurnal-trading-risk-calculator.xlsx',
                'file_path' => 'modules/documents/jurnal-trading-risk-calculator.xlsx',
                'disk' => 'r2',
                'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'size_bytes' => 2_300_000, // 2.3 MB
                'type' => 'document',
                'collection' => 'modules',
                'alt_text' => 'Excel spreadsheet kalkulator risiko trading',
                'description' => 'Spreadsheet lengkap dengan formula otomatis winrate, profit factor, dan drawdowns.',
                'metadata' => [
                    'sheets' => ['Jurnal', 'Statistik', 'Risk Calculator'],
                ],
            ],
        ];

        foreach ($assets as $assetData) {
            $publicUrl = $r2->generatePublicUrl($assetData['file_path']);

            MediaAsset::updateOrCreate(
                ['file_path' => $assetData['file_path']],
                array_merge($assetData, [
                    'public_url' => $publicUrl,
                    'uploaded_by' => $adminId,
                ])
            );
        }
    }
}
