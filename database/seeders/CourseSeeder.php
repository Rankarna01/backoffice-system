<?php

namespace Database\Seeders;

use App\Domain\Learning\Models\Category;
use App\Domain\Learning\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds for Courses.
     */
    public function run(): void
    {
        $mentor1 = User::where('email', 'alex@tradingedu.com')->first();
        $mentor2 = User::where('email', 'sarah@tradingedu.com')->first();

        $catSmc = Category::where('slug', 'price-action-smc')->first();
        $catForex = Category::where('slug', 'forex-mastery')->first();
        $catGold = Category::where('slug', 'gold-commodities')->first();
        $catCrypto = Category::where('slug', 'crypto-derivatives')->first();

        // 1. Smart Money Concept & Liquidity Mastery
        Course::updateOrCreate(
            ['slug' => 'smart-money-concepts-mastery'],
            [
                'mentor_id' => $mentor1?->id,
                'category_id' => $catSmc?->id,
                'title' => 'Smart Money Concept & Liquidity Mastery',
                'subtitle' => 'Panduan lengkap membaca jejak transaksi institusi besar dan institutional order flow.',
                'description' => "Pelajari cara kerja pasar finansial dari perspektif bank dan institusi global. Kursus ini membahas struktur pasar, order blocks, fair value gaps, liquidity grabs, serta setup entry probabilitas tinggi dengan risiko minimal.\n\nMateri dilengkapi studi kasus nyata pada pasangan Forex utama (EURUSD, GBPUSD) dan Indeks Global.",
                'preview_video_ref' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'level' => 'intermediate',
                'language' => 'id',
                'access_type' => 'paid',
                'price' => 1490000,
                'compare_at_price' => 2490000,
                'currency' => 'IDR',
                'is_sequential' => true,
                'has_certificate' => true,
                'status' => 'published',
                'duration_seconds' => 18600,
                'lessons_count' => 24,
                'students_count' => 412,
                'rating_avg' => 4.92,
                'rating_count' => 128,
                'published_at' => now()->subMonths(2),
            ]
        );

        // 2. Forex Trading Basics for Beginners
        Course::updateOrCreate(
            ['slug' => 'forex-trading-basics-beginners'],
            [
                'mentor_id' => $mentor1?->id,
                'category_id' => $catForex?->id,
                'title' => 'Forex Trading Basics for Beginners',
                'subtitle' => 'Langkah awal memahami pips, lot, leverage, dan eksekusi market yang aman bagi pemula.',
                'description' => "Kursus fundamental gratis bagi siapa saja yang ingin memulai trading forex dari nol secara profesional. Pelajari terminologi pasar, membaca chart candlestick, memilih broker teregulasi, dan kalkulasi risiko dasar.",
                'level' => 'beginner',
                'language' => 'id',
                'access_type' => 'free',
                'price' => 0,
                'currency' => 'IDR',
                'is_sequential' => false,
                'has_certificate' => true,
                'status' => 'published',
                'duration_seconds' => 7200,
                'lessons_count' => 12,
                'students_count' => 1280,
                'rating_avg' => 4.85,
                'rating_count' => 310,
                'published_at' => now()->subMonths(3),
            ]
        );

        // 3. Gold (XAUUSD) Swing & Day Trading Playbook
        Course::updateOrCreate(
            ['slug' => 'gold-xauusd-trading-playbook'],
            [
                'mentor_id' => $mentor2?->id,
                'category_id' => $catGold?->id,
                'title' => 'Gold (XAUUSD) Swing & Day Trading Playbook',
                'subtitle' => 'Strategi momentum dan analisa fundamental makro ekonomi khusus komoditas emas.',
                'description' => "Emas adalah salah satu instrumen paling volatil dan menguntungkan jika diperdagangkan dengan benar. Kuasai korelasi US Dollar (DXY), imbal hasil obligasi AS (US10Y), rilis data CPI/NFP, dan strategi breakout sesi London/New York.",
                'level' => 'advanced',
                'language' => 'id',
                'access_type' => 'paid',
                'price' => 1990000,
                'compare_at_price' => 2990000,
                'currency' => 'IDR',
                'is_sequential' => true,
                'has_certificate' => true,
                'status' => 'published',
                'duration_seconds' => 14400,
                'lessons_count' => 18,
                'students_count' => 256,
                'rating_avg' => 4.95,
                'rating_count' => 84,
                'published_at' => now()->subMonth(),
            ]
        );

        // 4. Crypto Derivatives & Order Flow Trading
        Course::updateOrCreate(
            ['slug' => 'crypto-derivatives-order-flow'],
            [
                'mentor_id' => $mentor2?->id,
                'category_id' => $catCrypto?->id,
                'title' => 'Crypto Derivatives & Order Flow Trading',
                'subtitle' => 'Teknik analisa volume delta, funding rates, dan likuidasi di pasar futures kripto.',
                'description' => "Memahami data on-chain dan derivatif kripto untuk mendeteksi pergerakan whale di Bitcoin & Ethereum.",
                'level' => 'advanced',
                'language' => 'id',
                'access_type' => 'subscription',
                'price' => 0,
                'currency' => 'IDR',
                'is_sequential' => false,
                'has_certificate' => true,
                'status' => 'in_review',
                'duration_seconds' => 10800,
                'lessons_count' => 15,
                'students_count' => 0,
                'rating_avg' => 0.00,
                'rating_count' => 0,
            ]
        );
    }
}
