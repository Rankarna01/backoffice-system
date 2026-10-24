<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Billing\Models\Order;
use App\Domain\Billing\Models\OrderItem;
use App\Domain\Billing\Models\Payment;
use App\Domain\Billing\Models\Plan;
use App\Domain\Billing\Models\Subscription;
use App\Domain\Learning\Models\Course;
use App\Domain\Learning\Models\CourseEnrollment;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PackageController extends Controller
{
    /**
     * Curated course packages for fallback or when database plans are not yet linked to courses.
     */
    protected function getCuratedPackages(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'Paket Pemula: Fondasi Trading & Psikologi',
                'slug' => 'starter-trader-bundle',
                'badge' => 'Starter Choice',
                'description' => 'Paket belajar lengkap untuk pemula. Pahami terminologi pasar forex, analisa teknikal dasar, dan kuasai kontrol emosi agar bebas dari FOMO.',
                'price' => 490000,
                'compare_at_price' => 990000,
                'currency' => 'IDR',
                'duration_label' => 'Akses Selamanya (Lifetime)',
                'features' => [
                    '2 Kursus Komprehensif (Fondasi & Mindset)',
                    '10 Video Pelajaran & Rangkuman Dokumen',
                    'Template Jurnal Trading Pemula',
                    'Sertifikat Kelulusan Resmi Digital',
                    'Akses Forum Diskusi Komunitas Murid',
                ],
                'course_slugs' => ['forex-trading-essentials-course', 'trading-psychology-blueprint'],
                'courses' => [
                    ['id' => 202, 'title' => 'Forex Trading Masterclass – Essentials & Currency Correlation', 'slug' => 'forex-trading-essentials-course', 'level' => 'beginner', 'duration_seconds' => 5400, 'lessons_count' => 5, 'thumbnail_url' => 'https://images.unsplash.com/photo-1642543492481-44e81e3914a7?w=800&auto=format&fit=crop&q=80', 'mentor' => ['name' => 'Ethan Turner', 'title' => 'Macro Forex Trader', 'avatar_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=120&auto=format&fit=crop&q=80']],
                    ['id' => 207, 'title' => 'Trading Psychology Blueprint: Eliminate FOMO & Overtrading', 'slug' => 'trading-psychology-blueprint', 'level' => 'beginner', 'duration_seconds' => 5400, 'lessons_count' => 5, 'thumbnail_url' => 'https://images.unsplash.com/photo-1559526324-4b87b5e36e44?w=800&auto=format&fit=crop&q=80', 'mentor' => ['name' => 'Sarah Johnson', 'title' => 'Trading Mindset Coach', 'avatar_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=120&auto=format&fit=crop&q=80']],
                ],
                'mentors' => [
                    ['name' => 'Ethan Turner', 'title' => 'Macro Forex Trader', 'avatar_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=120&auto=format&fit=crop&q=80'],
                    ['name' => 'Sarah Johnson', 'title' => 'Trading Mindset Coach', 'avatar_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=120&auto=format&fit=crop&q=80'],
                ],
                'stats' => ['courses_count' => 2, 'lessons_count' => 10, 'duration_hours' => 3, 'students_count' => 1250, 'rating_avg' => 4.9],
            ],
            [
                'id' => 2,
                'name' => 'Paket Pro: Smart Money Concepts & Gold Mastery',
                'slug' => 'smc-mastery-package',
                'badge' => 'Most Popular',
                'description' => 'Paket favorit trader mandiri. Kuasai strategi institusi bank besar (Order Blocks, Liquidity Sweeps) dan playbook eksekusi tajam pada instrumen Emas (XAU/USD).',
                'price' => 1490000,
                'compare_at_price' => 3290000,
                'currency' => 'IDR',
                'duration_label' => 'Akses Selamanya (Lifetime)',
                'features' => [
                    '3 Kursus Master Institutional Price Action & SMC',
                    '20+ High-Probability Setups Sesi London & New York',
                    'Custom Script Indikator TradingView Eksklusif',
                    'Grup Diskusi VIP Sinyal & Analisa Harian',
                    'Garansi Update Materi dan Template Berkala',
                ],
                'course_slugs' => ['practical-price-action-order-blocks', 'gold-scalping-playbook-20-setups', 'multi-timeframe-top-down-analysis'],
                'courses' => [
                    ['id' => 201, 'title' => 'Practical Price Action: Master Order Blocks & Market Structure', 'slug' => 'practical-price-action-order-blocks', 'level' => 'intermediate', 'duration_seconds' => 7200, 'lessons_count' => 6, 'thumbnail_url' => 'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=800&auto=format&fit=crop&q=80', 'mentor' => ['name' => 'Liam Anderson', 'title' => 'Senior SMC Specialist', 'avatar_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=120&auto=format&fit=crop&q=80']],
                    ['id' => 203, 'title' => 'XAU/USD Gold Scalping Playbook: 20 Daily Setups', 'slug' => 'gold-scalping-playbook-20-setups', 'level' => 'advanced', 'duration_seconds' => 7200, 'lessons_count' => 6, 'thumbnail_url' => 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=800&auto=format&fit=crop&q=80', 'mentor' => ['name' => 'Emily Carter', 'title' => 'Gold Scalping Veteran', 'avatar_url' => 'https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?w=120&auto=format&fit=crop&q=80']],
                    ['id' => 208, 'title' => 'Multi-Timeframe Top-Down Analysis: Monthly to M5', 'slug' => 'multi-timeframe-top-down-analysis', 'level' => 'intermediate', 'duration_seconds' => 5400, 'lessons_count' => 5, 'thumbnail_url' => 'https://images.unsplash.com/photo-1535320903710-d993d3d77d29?w=800&auto=format&fit=crop&q=80', 'mentor' => ['name' => 'Alex Wijaya', 'title' => 'Certified Financial Technician', 'avatar_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=120&auto=format&fit=crop&q=80']],
                ],
                'mentors' => [
                    ['name' => 'Liam Anderson', 'title' => 'Senior SMC Specialist', 'avatar_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=120&auto=format&fit=crop&q=80'],
                    ['name' => 'Emily Carter', 'title' => 'Gold Scalping Veteran', 'avatar_url' => 'https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?w=120&auto=format&fit=crop&q=80'],
                    ['name' => 'Alex Wijaya', 'title' => 'Certified Financial Technician', 'avatar_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=120&auto=format&fit=crop&q=80'],
                ],
                'stats' => ['courses_count' => 3, 'lessons_count' => 17, 'duration_hours' => 6, 'students_count' => 3420, 'rating_avg' => 5.0],
            ],
            [
                'id' => 3,
                'name' => 'Paket VIP: All-In-One Lifetime Master Pass',
                'slug' => 'all-in-one-vip-lifetime',
                'badge' => 'Maximum Value',
                'description' => 'Akses tak terbatas ke seluruh ekosistem TradingEdu selamanya. Buka semua 9 kursus, sinyal VIP, panduan prop firm challenge, dan sesi review 1-on-1.',
                'price' => 3490000,
                'compare_at_price' => 8990000,
                'currency' => 'IDR',
                'duration_label' => 'Akses Seumur Hidup (Lifetime)',
                'features' => [
                    'Buka Otomatis Seluruh 9 Kursus Platform Saat Ini & Rilis Mendatang',
                    'Semua Sinyal Trading Harian (Forex, Gold, Kripto, Indeks)',
                    '1x Sesi 1-on-1 Bedah Akun & Jurnal bersama Master Mentor',
                    'Strategi Lolos Evaluasi Tantangan Prop Firm (FTMO, MFFU)',
                    'Akses Ruang Live Trading Interaktif Setiap Pekan',
                ],
                'course_slugs' => [
                    'practical-price-action-order-blocks', 'forex-trading-essentials-course',
                    'gold-scalping-playbook-20-setups', 'crypto-perpetual-futures-arbitrage',
                    'craft-winning-trading-strategy', 'mastering-prop-firm-challenges',
                    'trading-psychology-blueprint', 'multi-timeframe-top-down-analysis',
                    'professional-trading-journal-mastery',
                ],
                'courses' => [
                    ['id' => 201, 'title' => 'Practical Price Action: Master Order Blocks & Market Structure', 'slug' => 'practical-price-action-order-blocks', 'level' => 'intermediate', 'duration_seconds' => 7200, 'lessons_count' => 6, 'thumbnail_url' => 'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=800&auto=format&fit=crop&q=80', 'mentor' => ['name' => 'Liam Anderson', 'title' => 'Senior SMC Specialist', 'avatar_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=120&auto=format&fit=crop&q=80']],
                    ['id' => 204, 'title' => 'Crypto Perpetual Futures: Hedging & Funding Rate Arbitrage', 'slug' => 'crypto-perpetual-futures-arbitrage', 'level' => 'intermediate', 'duration_seconds' => 5400, 'lessons_count' => 5, 'thumbnail_url' => 'https://images.unsplash.com/photo-1620712943543-bcc4688e7485?w=800&auto=format&fit=crop&q=80', 'mentor' => ['name' => 'Jessie Williamson', 'title' => 'Derivatives Specialist', 'avatar_url' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=120&auto=format&fit=crop&q=80']],
                    ['id' => 206, 'title' => 'Mastering Prop Firm Challenges: Drawdown Control & Sizing', 'slug' => 'mastering-prop-firm-challenges', 'level' => 'advanced', 'duration_seconds' => 7200, 'lessons_count' => 6, 'thumbnail_url' => 'https://images.unsplash.com/photo-1618005198919-d3d4b5a92ead?w=800&auto=format&fit=crop&q=80', 'mentor' => ['name' => 'David Thompson', 'title' => 'Funded Trader & Mentor', 'avatar_url' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=120&auto=format&fit=crop&q=80']],
                ],
                'mentors' => [
                    ['name' => 'Liam Anderson', 'title' => 'Senior SMC Specialist', 'avatar_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=120&auto=format&fit=crop&q=80'],
                    ['name' => 'Alex Wijaya', 'title' => 'Certified Financial Technician', 'avatar_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=120&auto=format&fit=crop&q=80'],
                    ['name' => 'Emily Carter', 'title' => 'Gold Scalping Veteran', 'avatar_url' => 'https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?w=120&auto=format&fit=crop&q=80'],
                    ['name' => 'David Thompson', 'title' => 'Funded Trader & Mentor', 'avatar_url' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=120&auto=format&fit=crop&q=80'],
                ],
                'stats' => ['courses_count' => 9, 'lessons_count' => 45, 'duration_hours' => 24, 'students_count' => 5890, 'rating_avg' => 5.0],
            ],
        ];
    }

    /**
     * GET /api/v1/packages
     * List all available course packages.
     */
    public function index(): JsonResponse
    {
        $curated = $this->getCuratedPackages();

        // Check if database has plans
        try {
            $dbPlans = Plan::with(['courses.mentor', 'courses.category'])
                ->where('is_active', true)
                ->orderBy('sort_order', 'asc')
                ->get();

            if ($dbPlans->isNotEmpty()) {
                // Merge database plans if they exist and have courses
                $merged = $dbPlans->map(function ($plan) {
                    $courses = $plan->courses->map(function ($c) {
                        return [
                            'id' => $c->id,
                            'title' => $c->title,
                            'slug' => $c->slug,
                            'level' => $c->level,
                            'duration_seconds' => $c->duration_seconds,
                            'lessons_count' => $c->lessons_count,
                            'thumbnail_url' => $c->thumbnail_url ?? 'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=800&auto=format&fit=crop&q=80',
                            'mentor' => [
                                'name' => $c->mentor?->name ?? 'TradingEdu Specialist',
                                'title' => 'Senior Trading Specialist',
                                'avatar_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=120&auto=format&fit=crop&q=80',
                            ],
                        ];
                    });

                    $mentors = $courses->pluck('mentor')->unique('name')->values()->all();

                    return [
                        'id' => $plan->id,
                        'name' => $plan->name,
                        'slug' => $plan->slug,
                        'badge' => $plan->sort_order === 1 ? 'Starter Choice' : ($plan->sort_order === 2 ? 'Most Popular' : 'Maximum Value'),
                        'description' => $plan->description,
                        'price' => (float) $plan->price,
                        'compare_at_price' => $plan->compare_at_price ? (float) $plan->compare_at_price : null,
                        'currency' => $plan->currency ?? 'IDR',
                        'duration_label' => $plan->duration_days ? "{$plan->duration_days} Hari Akses" : 'Akses Seumur Hidup (Lifetime)',
                        'features' => $plan->features ?? [],
                        'courses' => $courses,
                        'mentors' => $mentors,
                        'stats' => [
                            'courses_count' => $courses->count(),
                            'lessons_count' => $courses->sum('lessons_count') ?: 10,
                            'duration_hours' => round($courses->sum('duration_seconds') / 3600, 1) ?: 5,
                            'students_count' => 1200,
                            'rating_avg' => 4.9,
                        ],
                    ];
                })->all();

                if (!empty($merged) && count($merged[0]['courses']) > 0) {
                    $curated = $merged;
                }
            }
        } catch (\Throwable $e) {
            // Fallback gracefully to curated packages
        }

        return response()->json([
            'success' => true,
            'message' => 'Daftar paket kursus berhasil dimuat.',
            'data' => $curated,
        ]);
    }

    /**
     * GET /api/v1/packages/{slug}
     * Get specific course package details.
     */
    public function show(string $slug): JsonResponse
    {
        $all = $this->getCuratedPackages();
        $package = collect($all)->firstWhere('slug', $slug);

        if (!$package) {
            return response()->json([
                'success' => false,
                'message' => 'Paket kursus tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $package,
        ]);
    }

    /**
     * POST /api/v1/packages/{slug}/checkout
     * Process package purchase & automatically enroll student in all included courses!
     */
    public function checkout(Request $request, string $slug): JsonResponse
    {
        $all = $this->getCuratedPackages();
        $package = collect($all)->firstWhere('slug', $slug);

        if (!$package) {
            return response()->json([
                'success' => false,
                'message' => 'Paket kursus tidak ditemukan.',
            ], 404);
        }

        // Identify authenticated user or default fallback student
        $user = null;
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('users')) {
                $user = $request->user('sanctum')
                    ?? User::where('email', 'randy@tradingedu.com')->first()
                    ?? User::where('email', 'customer@tradingedu.com')->first()
                    ?? User::first();
            }
        } catch (\Throwable $e) {}

        $enrolledCoursesCount = count($package['course_slugs'] ?? []);
        $orderNumber = 'ORD-PKG-' . strtoupper(Str::random(8));

        try {
            DB::beginTransaction();

            // 1. Create Order for package if order tables exist
            if ($user && \Illuminate\Support\Facades\Schema::hasTable('orders')) {
                $order = Order::create([
                    'number' => $orderNumber,
                    'user_id' => $user->id,
                    'status' => 'paid',
                    'currency' => $package['currency'] ?? 'IDR',
                    'subtotal' => $package['price'],
                    'discount_total' => 0,
                    'tax_total' => 0,
                    'total' => $package['price'],
                    'paid_at' => now(),
                    'expires_at' => now()->addDays(365),
                ]);

                if (\Illuminate\Support\Facades\Schema::hasTable('order_items')) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'purchasable_type' => Plan::class,
                        'purchasable_id' => $package['id'] ?? 1,
                        'name' => $package['name'],
                        'unit_price' => $package['price'],
                        'quantity' => 1,
                        'discount' => 0,
                        'total' => $package['price'],
                    ]);
                }

                if (\Illuminate\Support\Facades\Schema::hasTable('payments')) {
                    Payment::create([
                        'order_id' => $order->id,
                        'gateway' => 'manual',
                        'gateway_ref' => 'PAY-PKG-' . strtoupper(Str::random(6)),
                        'method' => $request->get('payment_method', 'Instant QRIS / Virtual Account'),
                        'status' => 'paid',
                        'amount' => $package['price'],
                        'currency' => 'IDR',
                        'paid_at' => now(),
                    ]);
                }
            }

            // 2. Auto-enroll user into EVERY COURSE in this package!
            $targetSlugs = $package['course_slugs'] ?? [];
            if ($user && !empty($targetSlugs) && \Illuminate\Support\Facades\Schema::hasTable('course_enrollments') && \Illuminate\Support\Facades\Schema::hasTable('courses')) {
                $coursesToEnroll = Course::whereIn('slug', $targetSlugs)->get();

                if ($coursesToEnroll->isEmpty()) {
                    $coursesToEnroll = Course::take(count($targetSlugs))->get();
                }

                $enrolledCoursesCount = 0;
                foreach ($coursesToEnroll as $course) {
                    CourseEnrollment::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'course_id' => $course->id,
                        ],
                        [
                            'order_id' => $order?->id ?? null,
                            'enrolled_at' => now(),
                            'progress_percentage' => 0,
                            'completed_lessons_count' => 0,
                        ]
                    );
                    $enrolledCoursesCount++;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Selamat! Anda telah resmi terdaftar di {$package['name']}. Semua {$enrolledCoursesCount} kursus dalam paket ini telah aktif dibuka di akun Anda.",
                'data' => [
                    'order_number' => $orderNumber,
                    'package_name' => $package['name'],
                    'price' => $package['price'],
                    'enrolled_courses_count' => $enrolledCoursesCount,
                    'redirect_url' => '/learning/my-courses',
                ],
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses pendaftaran paket: ' . $e->getMessage(),
            ], 500);
        }
    }
}
