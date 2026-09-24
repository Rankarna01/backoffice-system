<?php

namespace Database\Seeders;

use App\Domain\Identity\Models\MentorProfile;
use App\Domain\Learning\Models\Category;
use App\Domain\Learning\Models\Course;
use App\Domain\Learning\Models\Module;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Create Permissions per 01-Business-Flow.md & 03-Backend-Architecture.md
        $permissions = [
            // General / Dashboard
            'view-dashboard',
            'manage-users',
            'manage-courses',
            'manage-signals',
            'manage-orders',
            'manage-community',
            'manage-settings',
            'access-learning',
            'access-trading-tools',
            'access-market-signals',

            // User Management
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'mentors.manage',
            'roles.manage',

            // Learning & Courses
            'courses.view',
            'courses.create',
            'courses.update',
            'courses.delete',
            'courses.publish',
            'modules.manage',
            'lessons.manage',
            'quizzes.manage',
            'certificates.manage',

            // Market & Signals
            'signals.view',
            'signals.create',
            'signals.update',
            'signals.delete',
            'signals.publish',
            'market_outlooks.manage',
            'news.manage',
            'economic_calendar.manage',

            // Community & Live Sessions
            'discussions.manage',
            'live_sessions.manage',

            // Monetization & Sales
            'orders.view',
            'orders.update',
            'orders.refund',
            'subscriptions.manage',
            'coupons.manage',
            'affiliates.manage',

            // Content & Website
            'announcements.manage',
            'reviews.manage',
            'testimonials.manage',
            'faqs.manage',
            'landing_page.manage',

            // System & Settings
            'settings.view',
            'settings.update',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
        }

        // 3. Create Roles per 06-Database-Schema.md: super_admin, admin, mentor, customer
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $mentorRole = Role::firstOrCreate(['name' => 'mentor', 'guard_name' => 'web']);
        $customerRole = Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

        $superAdminRole->syncPermissions(Permission::all());

        $adminRole->syncPermissions([
            'view-dashboard',
            'manage-users',
            'manage-courses',
            'manage-signals',
            'manage-orders',
            'manage-community',
            'users.view',
            'users.create',
            'users.update',
            'mentors.manage',
            'courses.view',
            'courses.create',
            'courses.update',
            'courses.delete',
            'courses.publish',
            'modules.manage',
            'lessons.manage',
            'quizzes.manage',
            'certificates.manage',
            'signals.view',
            'signals.create',
            'signals.update',
            'signals.publish',
            'market_outlooks.manage',
            'news.manage',
            'economic_calendar.manage',
            'discussions.manage',
            'live_sessions.manage',
            'orders.view',
            'orders.update',
            'orders.refund',
            'subscriptions.manage',
            'coupons.manage',
            'affiliates.manage',
            'announcements.manage',
            'reviews.manage',
            'testimonials.manage',
            'faqs.manage',
            'landing_page.manage',
            'settings.view',
        ]);

        $mentorRole->syncPermissions([
            'view-dashboard',
            'manage-courses',
            'manage-signals',
            'manage-community',
            'courses.view',
            'courses.create',
            'courses.update',
            'modules.manage',
            'lessons.manage',
            'quizzes.manage',
            'signals.view',
            'signals.create',
            'signals.update',
            'signals.publish',
            'market_outlooks.manage',
            'live_sessions.manage',
            'discussions.manage',
            'reviews.manage',
            'access-learning',
            'access-trading-tools',
            'access-market-signals',
        ]);

        $customerRole->syncPermissions([
            'access-learning',
            'access-trading-tools',
            'access-market-signals',
        ]);

        // 4. Seed Super Admin User
        $superAdmin = User::updateOrCreate(
            ['email' => 'admin@tradingedu.com'],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Super Admin TradingEdu',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'phone' => '+628111111111',
                'timezone' => 'Asia/Jakarta',
                'locale' => 'id',
                'status' => 'active',
                'terms_accepted_version' => '1.0',
            ]
        );
        $superAdmin->syncRoles([$superAdminRole]);

        // 5. Seed Operational Admin User
        $operationalAdmin = User::updateOrCreate(
            ['email' => 'staff@tradingedu.com'],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Admin Operasional',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'phone' => '+628122222222',
                'timezone' => 'Asia/Jakarta',
                'locale' => 'id',
                'status' => 'active',
                'terms_accepted_version' => '1.0',
            ]
        );
        $operationalAdmin->syncRoles([$adminRole]);

        // 6. Seed Mentors with Profiles
        $mentor1 = User::updateOrCreate(
            ['email' => 'alex@tradingedu.com'],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Alex Wijaya (Master Mentor)',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'phone' => '+628133333333',
                'timezone' => 'Asia/Jakarta',
                'locale' => 'id',
                'status' => 'active',
                'terms_accepted_version' => '1.0',
            ]
        );
        $mentor1->syncRoles([$mentorRole]);

        MentorProfile::updateOrCreate(
            ['user_id' => $mentor1->id],
            [
                'headline' => 'Certified Financial Technician & Full-Time Price Action Trader',
                'bio' => 'Pengalaman 10+ tahun trading di pasar Forex dan Indeks Global. Spesialis analisa struktur pasar, likuiditas institusional, dan manajemen risiko ketat.',
                'expertise' => ['Price Action', 'Forex Major Pairs', 'Smart Money Concepts', 'Risk Management'],
                'social_links' => [
                    'instagram' => '@alexwijaya_fx',
                    'youtube' => 'AlexWijayaTrading',
                    'telegram' => 't.me/alexwijayafx',
                ],
                'is_featured' => true,
                'sort_order' => 1,
            ]
        );

        $mentor2 = User::updateOrCreate(
            ['email' => 'sarah@tradingedu.com'],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Sarah Tan (Commodities & Crypto Mentor)',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'phone' => '+628144444444',
                'timezone' => 'Asia/Jakarta',
                'locale' => 'id',
                'status' => 'active',
                'terms_accepted_version' => '1.0',
            ]
        );
        $mentor2->syncRoles([$mentorRole]);

        MentorProfile::updateOrCreate(
            ['user_id' => $mentor2->id],
            [
                'headline' => 'Gold (XAUUSD) & Crypto Derivatives Specialist',
                'bio' => 'Fokus pada strategi swing & momentum di instrumen Emas (XAUUSD), Minyak Mentah, dan aset kripto utama dengan integrasi sentimen makro.',
                'expertise' => ['Gold (XAUUSD)', 'Cryptocurrency', 'Order Flow Analysis', 'Macro Economics'],
                'social_links' => [
                    'instagram' => '@sarahtan_trader',
                    'youtube' => 'SarahTanCommodities',
                ],
                'is_featured' => true,
                'sort_order' => 2,
            ]
        );

        // 7. Seed Customer / Member Accounts
        $customer1 = User::updateOrCreate(
            ['email' => 'customer@tradingedu.com'],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Budi Santoso',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'phone' => '+628155555555',
                'timezone' => 'Asia/Jakarta',
                'locale' => 'id',
                'status' => 'active',
                'terms_accepted_version' => '1.0',
            ]
        );
        $customer1->syncRoles([$customerRole]);

        $customer2 = User::updateOrCreate(
            ['email' => 'dewi@tradingedu.com'],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Dewi Lestari',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'phone' => '+628166666666',
                'timezone' => 'Asia/Jakarta',
                'locale' => 'id',
                'status' => 'active',
                'terms_accepted_version' => '1.0',
            ]
        );
        $customer2->syncRoles([$customerRole]);

        // 8. Seed Course Categories per 06-Database-Schema.md
        $catSmc = Category::firstOrCreate(
            ['type' => 'course', 'slug' => 'price-action-smc'],
            ['name' => 'Price Action & Smart Money Concepts', 'sort_order' => 1, 'is_active' => true]
        );

        $catForex = Category::firstOrCreate(
            ['type' => 'course', 'slug' => 'forex-mastery'],
            ['name' => 'Forex Market Mastery', 'sort_order' => 2, 'is_active' => true]
        );

        $catGold = Category::firstOrCreate(
            ['type' => 'course', 'slug' => 'gold-commodities'],
            ['name' => 'Gold & Commodities Strategy', 'sort_order' => 3, 'is_active' => true]
        );

        $catCrypto = Category::firstOrCreate(
            ['type' => 'course', 'slug' => 'crypto-derivatives'],
            ['name' => 'Cryptocurrency Derivatives', 'sort_order' => 4, 'is_active' => true]
        );

        $catRisk = Category::firstOrCreate(
            ['type' => 'course', 'slug' => 'psychology-risk'],
            ['name' => 'Trading Psychology & Risk Management', 'sort_order' => 5, 'is_active' => true]
        );

        // 9. Seed Sample Courses
        $courseSmc = Course::updateOrCreate(
            ['slug' => 'smart-money-concepts-mastery'],
            [
                'mentor_id' => $mentor1->id,
                'category_id' => $catSmc->id,
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

        $courseForex = Course::updateOrCreate(
            ['slug' => 'forex-trading-basics-beginners'],
            [
                'mentor_id' => $mentor1->id,
                'category_id' => $catForex->id,
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

        $courseGold = Course::updateOrCreate(
            ['slug' => 'gold-xauusd-trading-playbook'],
            [
                'mentor_id' => $mentor2->id,
                'category_id' => $catGold->id,
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

        $courseCrypto = Course::updateOrCreate(
            ['slug' => 'crypto-derivatives-order-flow'],
            [
                'mentor_id' => $mentor2->id,
                'category_id' => $catCrypto->id,
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

        // 10. Seed Modules for Courses
        $smcModules = [
            [
                'title' => 'Bab 1: Fondasi & Logika Pasar Smart Money',
                'description' => 'Memahami institusi vs retail, mekanisme likuiditas, dan siklus akumulasi distribusi.',
                'sort_order' => 1,
                'is_published' => true,
            ],
            [
                'title' => 'Bab 2: Market Structure, BOS, dan CHoCH',
                'description' => 'Membaca Break of Structure (BOS), Change of Character (CHoCH), dan validasi swing high/low.',
                'sort_order' => 2,
                'is_published' => true,
            ],
            [
                'title' => 'Bab 3: Liquidity Pools & Inducement',
                'description' => 'Mendeteksi rekayasa likuiditas: Equal Highs/Lows, Buy-Side Liquidity (BSL), dan Sell-Side Liquidity (SSL).',
                'sort_order' => 3,
                'is_published' => true,
            ],
            [
                'title' => 'Bab 4: Supply & Demand, Order Block, dan FVG',
                'description' => 'Identifikasi Order Block yang belum ter-mitigasi, Imbalance, dan Fair Value Gap (FVG).',
                'sort_order' => 4,
                'is_published' => true,
            ],
            [
                'title' => 'Bab 5: Multi-Timeframe Analysis & Sniper Entry Execution',
                'description' => 'Sinkronisasi Daily/H4 bias ke M15/M5/M1 entry trigger dengan konfirmasi mitigasi.',
                'sort_order' => 5,
                'is_published' => true,
            ],
        ];

        foreach ($smcModules as $mod) {
            Module::updateOrCreate(
                ['course_id' => $courseSmc->id, 'title' => $mod['title']],
                $mod
            );
        }

        $forexModules = [
            [
                'title' => 'Bab 1: Pengenalan Pasar Forex & Karakteristik Pasangan Mata Uang',
                'description' => 'Memahami pairs major, minor, dan exotic serta karakteristik sesi London dan New York.',
                'sort_order' => 1,
                'is_published' => true,
            ],
            [
                'title' => 'Bab 2: Kalkulasi Pip, Lot, Margin, dan Leverage',
                'description' => 'Rumus perhitungan lot size dan penggunaan leverage yang bijak agar modal terlindungi.',
                'sort_order' => 2,
                'is_published' => true,
            ],
            [
                'title' => 'Bab 3: Rule Manajemen Resiko 1% & Risk-Reward Ratio',
                'description' => 'Membangun disiplin stop loss dan target profit minimum 1:2 untuk pertumbuhan portofolio.',
                'sort_order' => 3,
                'is_published' => true,
            ],
            [
                'title' => 'Bab 4: Psikologi Trading & Jurnal Evaluasi',
                'description' => 'Mengontrol emosi trading, menghindari overtrading, dan membuat rutinitas jurnal berkala.',
                'sort_order' => 4,
                'is_published' => true,
            ],
        ];

        foreach ($forexModules as $mod) {
            Module::updateOrCreate(
                ['course_id' => $courseForex->id, 'title' => $mod['title']],
                $mod
            );
        }

        $goldModules = [
            [
                'title' => 'Bab 1: Karakteristik Volatilitas & Jam Trading Emas',
                'description' => 'Memahami pergerakan XAUUSD pada sesi London Fix dan pembukaan bursa komoditas AS.',
                'sort_order' => 1,
                'is_published' => true,
            ],
            [
                'title' => 'Bab 2: Pengaruh DXY, Yield US10Y, dan Data Inflasi CPI',
                'description' => 'Menganalisis sentimen makro ekonomi yang menggerakkan tren harga emas dunia.',
                'sort_order' => 2,
                'is_published' => true,
            ],
            [
                'title' => 'Bab 3: Setup Breakout & Scalping XAUUSD',
                'description' => 'Strategi eksekusi intraday momentum dengan stop loss ketat pada time frame M5-M15.',
                'sort_order' => 3,
                'is_published' => true,
            ],
        ];

        foreach ($goldModules as $mod) {
            Module::updateOrCreate(
                ['course_id' => $courseGold->id, 'title' => $mod['title']],
                $mod
            );
        }

        $cryptoModules = [
            [
                'title' => 'Bab 1: Pengenalan Pasar Derivatif Kripto & Perpetual Swaps',
                'description' => 'Mekanisme kontrak perpetual, funding rate, dan likuidasi bertingkat di exchange kripto.',
                'sort_order' => 1,
                'is_published' => true,
            ],
            [
                'title' => 'Bab 2: Analisa Order Book, Delta Volume, dan Open Interest',
                'description' => 'Mendeteksi pergerakan whale dan perangkap likuidasi jangka pendek.',
                'sort_order' => 2,
                'is_published' => false,
            ],
        ];

        foreach ($cryptoModules as $mod) {
            Module::updateOrCreate(
                ['course_id' => $courseCrypto->id, 'title' => $mod['title']],
                $mod
            );
        }
    }
}
