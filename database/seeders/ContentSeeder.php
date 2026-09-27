<?php

namespace Database\Seeders;

use App\Domain\Content\Enums\AnnouncementStatus;
use App\Domain\Content\Enums\AnnouncementType;
use App\Domain\Content\Enums\ReviewStatus;
use App\Domain\Content\Models\Announcement;
use App\Domain\Content\Models\Faq;
use App\Domain\Content\Models\Review;
use App\Domain\Content\Models\Testimonial;
use App\Domain\Learning\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $studentBudi = User::where('email', 'budi@student.com')->first() ?: $admin;
        $course = Course::first();

        // 1. Announcements
        Announcement::updateOrCreate(
            ['slug' => 'jadwal-live-trading-london-open-minggu-ini'],
            [
                'title' => 'Pembaruan Jadwal Live Trading London Open & Rilis Kalender Ekonomi Minggu Ini',
                'body' => "Halo rekan-rekan trader! Mengingat adanya rilis data inflasi CPI AS dan keputusan suku bunga The Fed pekan ini, sesi live market analysis dijadwalkan ulang menjadi setiap pukul 19:30 WIB.\n\nPastikan Anda telah mengaktifkan notifikasi di dashboard dan mengecek tab Economic Calendar sebelum sesi dimulai.",
                'type' => AnnouncementType::Info,
                'audience' => 'all',
                'is_pinned' => true,
                'starts_at' => now()->subDay(),
                'status' => AnnouncementStatus::Published,
                'created_by' => $admin?->id,
            ]
        );

        Announcement::updateOrCreate(
            ['slug' => 'promo-flash-sale-pro-trader-tahunan'],
            [
                'title' => 'Promo Spesial: Potongan 20% untuk Upgrade ke Paket Pro Trader Tahunan',
                'body' => "Gunakan kode kupon **TRADINGPRO20** pada saat checkout paket tahunan untuk menikmati diskon instan 20%. Promo terbatas untuk 100 member pertama!",
                'type' => AnnouncementType::Promo,
                'audience' => 'all',
                'is_pinned' => false,
                'starts_at' => now()->subDays(3),
                'ends_at' => now()->addWeeks(2),
                'status' => AnnouncementStatus::Published,
                'created_by' => $admin?->id,
            ]
        );

        Announcement::updateOrCreate(
            ['slug' => 'pemeliharaan-server-optimasi-database-minggu'],
            [
                'title' => 'Jadwal Pemeliharaan Server Rutin Akhir Pekan (Minggu Dini Hari)',
                'body' => "Akan dilakukan pemeliharaan server database untuk peningkatan performa feed grafik pada hari Minggu pukul 01:00 - 03:00 WIB. Selama durasi ini portal web mungkin tidak dapat diakses sementara.",
                'type' => AnnouncementType::Maintenance,
                'audience' => 'all',
                'is_pinned' => false,
                'starts_at' => now()->addDays(2),
                'status' => AnnouncementStatus::Published,
                'created_by' => $admin?->id,
            ]
        );

        // 2. Reviews
        if ($studentBudi && $course) {
            Review::updateOrCreate(
                ['user_id' => $studentBudi->id, 'course_id' => $course->id],
                [
                    'rating' => 5,
                    'title' => 'Materi SMC & Liquidity Sweep sangat aplikatif di market riil!',
                    'comment' => 'Penjelasan Coach Alex sangat terstruktur dan to-the-point. Sangat membantu saya memahami kenapa selama ini sering terkena stop hunt sebelum harga berbalik arah.',
                    'is_featured' => true,
                    'status' => ReviewStatus::Approved,
                    'approved_at' => now()->subDays(5),
                ]
            );

            Review::updateOrCreate(
                ['user_id' => $admin->id, 'course_id' => $course->id],
                [
                    'rating' => 5,
                    'title' => 'Silabus dan modul PDF sangat rapi',
                    'comment' => 'Lembar kerja manajemen risiko dan kalkulator compounding sangat praktis untuk mendisiplinkan jurnal trading harian.',
                    'is_featured' => false,
                    'status' => ReviewStatus::Approved,
                    'approved_at' => now()->subDays(10),
                ]
            );

            Review::updateOrCreate(
                ['user_id' => $studentBudi->id, 'course_id' => null],
                [
                    'rating' => 5,
                    'title' => 'Platform edukasi trading terbaik di Indonesia',
                    'comment' => 'Fitur sinyal real-time, kalender ekonomi, dan live webinar dalam satu portal membuat proses belajar menjadi sangat efisien.',
                    'is_featured' => true,
                    'status' => ReviewStatus::Approved,
                    'approved_at' => now()->subWeeks(2),
                ]
            );
        }

        // 3. Testimonials
        Testimonial::updateOrCreate(
            ['name' => 'Hendra Kurniawan'],
            [
                'role' => 'Full-Time Forex Trader • Profit Konsisten 8 Bulan',
                'avatar_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=200&auto=format&fit=crop',
                'quote' => 'Setelah 3 tahun gonta-ganti indikator dan selalu berakhir margin call, konsep Order Flow dan liquidity pool di TradingEdu mengubah mindset saya 180 derajat. Sekarang saya bisa trading dengan tenang tanpa rasa cemas.',
                'rating' => 5,
                'is_featured' => true,
                'sort_order' => 1,
                'is_published' => true,
            ]
        );

        Testimonial::updateOrCreate(
            ['name' => 'Dimas Pratama'],
            [
                'role' => 'Member VIP Mentorship • Gold & Commodities Specialist',
                'avatar_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&auto=format&fit=crop',
                'quote' => 'Sesi bedah chart langsung saat rilis NFP bersama mentor Alex benar-benar membuka mata. Saya belajar kapan harus entry dan yang lebih penting: kapan harus menahan diri untuk tidak trade.',
                'rating' => 5,
                'is_featured' => true,
                'sort_order' => 2,
                'is_published' => true,
            ]
        );

        Testimonial::updateOrCreate(
            ['name' => 'Rina Kusuma'],
            [
                'role' => 'Part-Time Trader & Mahasiswa Tingkat Akhir',
                'avatar_url' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=200&auto=format&fit=crop',
                'quote' => 'Materi disajikan dari nol tanpa jargon rumit. Modul PDF ringkas dan simulasi quiz membantu saya menguji pemahaman sebelum deposit uang riil.',
                'rating' => 5,
                'is_featured' => false,
                'sort_order' => 3,
                'is_published' => true,
            ]
        );

        // 4. FAQs
        Faq::updateOrCreate(
            ['question' => 'Apakah pemula tanpa latar belakang finansial bisa belajar di TradingEdu?'],
            [
                'category' => 'General',
                'answer' => "Tentu saja! Kurikulum TradingEdu disusun bertahap mulai dari pengenalan dasar (istilah pasar, cara membaca grafik candlestick, manajemen akun broker) hingga strategi institusional tingkat lanjut. Setiap materi dilengkapi video penjelasan dan modul rangkuman PDF.",
                'sort_order' => 1,
                'is_published' => true,
            ]
        );

        Faq::updateOrCreate(
            ['question' => 'Berapa lama masa aktif akses kursus yang telah saya beli?'],
            [
                'category' => 'Kursus & Materi',
                'answer' => "Setiap kursus satuan yang Anda beli memiliki akses seumur hidup (lifetime access) termasuk jika ada pembaruan video modul di masa depan tanpa biaya tambahan.",
                'sort_order' => 2,
                'is_published' => true,
            ]
        );

        Faq::updateOrCreate(
            ['question' => 'Bagaimana cara menerima sinyal trading harian?'],
            [
                'category' => 'Sinyal & Indikator',
                'answer' => "Sinyal trading harian akan otomatis muncul di menu Signals pada dashboard murid secara real-time lengkap dengan level Entry, Stop Loss, dan Take Profit bertahap, serta dapat diintegrasikan dengan notifikasi push.",
                'sort_order' => 3,
                'is_published' => true,
            ]
        );

        Faq::updateOrCreate(
            ['question' => 'Metode pembayaran apa saja yang didukung?'],
            [
                'category' => 'Langganan & Billing',
                'answer' => "Kami menerima pembayaran otomatis 24/7 melalui QRIS (GoPay, OVO, DANA, ShopeePay), Transfer Virtual Account seluruh bank utama di Indonesia (BCA, Mandiri, BNI, BRI), serta Kartu Kredit/Debit.",
                'sort_order' => 4,
                'is_published' => true,
            ]
        );
    }
}
