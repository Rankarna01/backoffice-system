<?php

namespace Database\Seeders;

use App\Domain\Community\Enums\DiscussionCategory;
use App\Domain\Community\Enums\DiscussionStatus;
use App\Domain\Community\Enums\ModerationStatus;
use App\Domain\Community\Models\Discussion;
use App\Domain\Community\Models\DiscussionReply;
use App\Domain\Learning\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DiscussionSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $mentorAlex = User::where('email', 'alex@tradingedu.com')->first() ?: $admin;
        $mentorSarah = User::where('email', 'sarah@tradingedu.com')->first() ?: $admin;
        $student = User::where('email', 'budi@student.com')->first() ?: $admin;
        $course = Course::first();

        // 1. Pinned Master Thread: Analisis XAUUSD
        $thread1 = Discussion::updateOrCreate(
            ['slug' => 'analisis-breakout-xauusd-menjelang-sesi-london'],
            [
                'user_id' => $mentorAlex->id,
                'course_id' => $course?->id,
                'title' => 'Analisis Breakout XAUUSD Menjelang Sesi London & Pivot 2650',
                'category' => DiscussionCategory::TechnicalAnalysis,
                'content' => "Halo rekan-rekan trader! Emas (XAUUSD) saat ini sedang berkonsolidasi ketat di rentang 2640-2655.\n\nPerhatikan liquidity pool di level high Asia. Jika terjadi sweep liquidity dengan rejection candle di TF M15/H1, kita bisa bersiap mencari konfirmasi sell setup menuju area demand 2630. Selalu gunakan stop loss terukur!",
                'status' => DiscussionStatus::Pinned,
                'moderation_status' => ModerationStatus::Approved,
                'is_pinned' => true,
                'is_locked' => false,
                'views_count' => 1420,
                'likes_count' => 86,
                'reports_count' => 0,
                'last_activity_at' => now()->subMinutes(15),
            ]
        );

        DiscussionReply::updateOrCreate(
            ['discussion_id' => $thread1->id, 'user_id' => $student->id],
            [
                'content' => 'Terima kasih Coach Alex! Di TF H4 terlihat ada Fair Value Gap (FVG) yang belum termitigasi di 2628. Apakah itu bisa jadi target take profit yang masuk akal?',
                'likes_count' => 12,
                'is_solution' => false,
                'is_hidden' => false,
            ]
        );

        DiscussionReply::updateOrCreate(
            ['discussion_id' => $thread1->id, 'user_id' => $mentorAlex->id],
            [
                'content' => 'Tepat sekali Budi! FVG H4 di 2628 sangat valid sebagai zona take profit akhir. Pastikan ambil sebagian profit (partial TP) di 2635 terlebih dahulu.',
                'likes_count' => 24,
                'is_solution' => true,
                'is_hidden' => false,
            ]
        );

        // 2. Thread Edukasi Psikologi & Risk Management
        $thread2 = Discussion::updateOrCreate(
            ['slug' => 'panduan-manajemen-risiko-max-2-persen-per-trade'],
            [
                'user_id' => $student->id,
                'course_id' => null,
                'title' => 'Mengapa Disiplin Max 1-2% Risiko Menyelamatkan Akun Saya dari Margin Call',
                'category' => DiscussionCategory::Psychology,
                'content' => "Bulan lalu saya mengalami 5 kekalahan beruntun (losing streak). Jika saya pakai lot besar, akun saya pasti sudah habis. Tapi karena strictly menerapkan risk 1.5% per trade, drawdown saya hanya sekitar 7.5%, dan berhasil recover dalam 2 minggu berikutnya.\n\nBagaimana aturan sizing dan risk per trade kalian teman-teman?",
                'status' => DiscussionStatus::Published,
                'moderation_status' => ModerationStatus::Approved,
                'is_pinned' => false,
                'is_locked' => false,
                'views_count' => 640,
                'likes_count' => 45,
                'reports_count' => 0,
                'last_activity_at' => now()->subHours(2),
            ]
        );

        DiscussionReply::updateOrCreate(
            ['discussion_id' => $thread2->id, 'user_id' => $mentorSarah->id],
            [
                'content' => 'Insight yang luar biasa! Trading bukan tentang seberapa cepat kita kaya, tapi tentang seberapa lama kita bertahan di pasar. Mindset ini yang membedakan trader profesional dan penjudi.',
                'likes_count' => 18,
                'is_solution' => true,
                'is_hidden' => false,
            ]
        );

        // 3. Thread Tanya Jawab Kursus (Q&A)
        Discussion::updateOrCreate(
            ['slug' => 'tanya-jawab-cara-validasi-order-block'],
            [
                'user_id' => $student->id,
                'course_id' => $course?->id,
                'title' => 'Pertanyaan Modul: Cara Membedakan Fakeout vs Valid Order Block',
                'category' => DiscussionCategory::Qna,
                'content' => "Saya sering terkecoh ketika harga masuk ke zona order block lalu langsung tembus (fake order block). Apakah ada indikasi volume atau konfirmasi candle sebelum entry?",
                'status' => DiscussionStatus::Published,
                'moderation_status' => ModerationStatus::Approved,
                'is_pinned' => false,
                'is_locked' => false,
                'views_count' => 310,
                'likes_count' => 19,
                'reports_count' => 0,
                'last_activity_at' => now()->subHours(5),
            ]
        );

        // 4. Thread Flagged / Butuh Moderasi (Untuk pengujian alur moderasi admin)
        Discussion::updateOrCreate(
            ['slug' => 'undangan-join-grup-sinyal-vip-eksternal'],
            [
                'user_id' => $student->id,
                'course_id' => null,
                'title' => 'Grup Sinyal Profit 100% Tiap Hari Tanpa Loss - Join Segera',
                'category' => DiscussionCategory::General,
                'content' => "Halo semua, join grup telegram eksternal saya untuk dapat sinyal otomatis tanpa analisa. Dijamin cuan puluhan juta per hari.",
                'status' => DiscussionStatus::Flagged,
                'moderation_status' => ModerationStatus::Flagged,
                'is_pinned' => false,
                'is_locked' => true,
                'views_count' => 88,
                'likes_count' => 0,
                'reports_count' => 6,
                'last_activity_at' => now()->subHours(10),
            ]
        );
    }
}
