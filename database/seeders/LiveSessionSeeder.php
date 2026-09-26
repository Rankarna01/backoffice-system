<?php

namespace Database\Seeders;

use App\Domain\Community\Enums\LiveSessionPlatform;
use App\Domain\Community\Enums\LiveSessionStatus;
use App\Domain\Community\Enums\LiveSessionTier;
use App\Domain\Community\Models\LiveSession;
use App\Domain\Community\Models\LiveSessionRegistration;
use App\Domain\Identity\Models\MentorProfile;
use App\Domain\Learning\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;

class LiveSessionSeeder extends Seeder
{
    public function run(): void
    {
        $mentorAlex = MentorProfile::whereHas('user', fn ($q) => $q->where('email', 'alex@tradingedu.com'))->first()
            ?: MentorProfile::first();
        $mentorSarah = MentorProfile::whereHas('user', fn ($q) => $q->where('email', 'sarah@tradingedu.com'))->first()
            ?: MentorProfile::first();
        $studentBudi = User::where('email', 'budi@student.com')->first()
            ?: User::first();
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $course = Course::first();

        // 1. Upcoming High-Impact Webinar (NFP Live)
        $session1 = LiveSession::updateOrCreate(
            ['slug' => 'live-nfp-trading-room-bedah-rilis-data-xauusd'],
            [
                'mentor_id' => $mentorAlex->id,
                'course_id' => $course?->id,
                'title' => 'Live NFP Trading Room: Bedah Rilis Data Tenaga Kerja AS & Setup Emas (XAUUSD)',
                'description' => "Non-Farm Payroll (NFP) selalu menjadi katalis volatilitas terbesar di pasar forex dan komoditas.\n\nDalam sesi live ini, Mentor Alex Wijaya akan memandu Anda membaca konsensus angka NFP vs aktual, mengidentifikasi liquidity sweep di awal rilis berita, dan mengeksekusi setup pullback setelah reaksi impulsif mereda.",
                'cover_image_url' => 'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=800&auto=format&fit=crop',
                'scheduled_at' => now()->addDays(2)->setHour(19)->setMinute(30)->setSecond(0),
                'duration_minutes' => 90,
                'platform' => LiveSessionPlatform::Zoom,
                'join_url' => 'https://zoom.us/j/89123456789',
                'passcode' => 'NFP2026',
                'max_participants' => 300,
                'status' => LiveSessionStatus::Upcoming,
                'is_featured' => true,
                'target_tier' => LiveSessionTier::All,
            ]
        );

        if ($studentBudi) {
            LiveSessionRegistration::updateOrCreate(
                ['live_session_id' => $session1->id, 'user_id' => $studentBudi->id],
                [
                    'registered_at' => now()->subDay(),
                    'attended' => false,
                    'notes' => 'Ingin menanyakan dampak NFP terhadap pair GBPUSD',
                ]
            );
        }

        // 2. Currently Live 🔴 Scalping Session
        $session2 = LiveSession::updateOrCreate(
            ['slug' => 'live-scalping-session-london-open-eurusd-gbpusd'],
            [
                'mentor_id' => $mentorAlex->id,
                'course_id' => null,
                'title' => 'Live Scalping Session: London Open Volatility & EURUSD / GBPUSD Breakout',
                'description' => "Live trading room saat pembukaan sesi pasar London. Kami fokus pada pair mayor dengan spread terendah, mencari order block M5 dan liquidity run di zona support/resistance Asia.",
                'cover_image_url' => 'https://images.unsplash.com/photo-1590283603385-17ffb3a7f29f?w=800&auto=format&fit=crop',
                'scheduled_at' => now()->subMinutes(20),
                'duration_minutes' => 75,
                'platform' => LiveSessionPlatform::Zoom,
                'join_url' => 'https://zoom.us/j/99223344556',
                'passcode' => 'SCALP2026',
                'max_participants' => 150,
                'status' => LiveSessionStatus::Live,
                'is_featured' => true,
                'target_tier' => LiveSessionTier::Pro,
            ]
        );

        if ($studentBudi) {
            LiveSessionRegistration::updateOrCreate(
                ['live_session_id' => $session2->id, 'user_id' => $studentBudi->id],
                [
                    'registered_at' => now()->subHours(4),
                    'attended' => true,
                    'attended_at' => now()->subMinutes(15),
                    'notes' => 'Hadir langsung di Zoom',
                ]
            );
        }
        if ($admin) {
            LiveSessionRegistration::updateOrCreate(
                ['live_session_id' => $session2->id, 'user_id' => $admin->id],
                [
                    'registered_at' => now()->subHours(6),
                    'attended' => true,
                    'attended_at' => now()->subMinutes(18),
                    'notes' => 'Host / Admin co-moderator',
                ]
            );
        }

        // 3. Completed Session with Replay
        $session3 = LiveSession::updateOrCreate(
            ['slug' => 'masterclass-risk-management-menghindari-margin-call'],
            [
                'mentor_id' => $mentorSarah->id,
                'course_id' => $course?->id,
                'title' => 'Masterclass Risk & Money Management: Cara Menghindari Margin Call Saat High Volatility',
                'description' => "Materi fundamental mengenai posisi lot sizing dinamis, korelasi risiko antar instrumen komoditas (Emas vs Minyak), dan penetapan batas stop loss maksimal harian.",
                'cover_image_url' => 'https://images.unsplash.com/photo-1642543492481-44e81e3914a7?w=800&auto=format&fit=crop',
                'scheduled_at' => now()->subDays(3)->setHour(19)->setMinute(0)->setSecond(0),
                'duration_minutes' => 95,
                'platform' => LiveSessionPlatform::YouTubeLive,
                'join_url' => 'https://youtube.com/live/archive-sarah-mm',
                'recording_url' => 'https://www.youtube.com/watch?v=live_replay_sample',
                'recording_duration_minutes' => 92,
                'max_participants' => 0,
                'status' => LiveSessionStatus::Completed,
                'is_featured' => false,
                'target_tier' => LiveSessionTier::All,
            ]
        );

        if ($studentBudi) {
            LiveSessionRegistration::updateOrCreate(
                ['live_session_id' => $session3->id, 'user_id' => $studentBudi->id],
                [
                    'registered_at' => now()->subDays(4),
                    'attended' => true,
                    'attended_at' => now()->subDays(3)->setHour(19)->setMinute(5),
                    'notes' => 'Telah menyimak seluruh materi',
                ]
            );
        }

        // 4. Upcoming VIP Mentorship Session
        $session4 = LiveSession::updateOrCreate(
            ['slug' => 'vip-portfolio-review-evaluasi-jurnal-trading'],
            [
                'mentor_id' => $mentorSarah->id,
                'course_id' => null,
                'title' => 'VIP Mentorship: Review Portofolio & Audit Jurnal Trading Mingguan',
                'description' => "Sesi eksklusif intensif khusus member VIP. Buka chart langsung bersama mentor untuk membedah winning vs losing trade Anda minggu ini secara objektif.",
                'cover_image_url' => 'https://images.unsplash.com/photo-1551836022-d5d88e9218df?w=800&auto=format&fit=crop',
                'scheduled_at' => now()->addDays(5)->setHour(20)->setMinute(0)->setSecond(0),
                'duration_minutes' => 60,
                'platform' => LiveSessionPlatform::GoogleMeet,
                'join_url' => 'https://meet.google.com/abc-defg-hij',
                'passcode' => null,
                'max_participants' => 10,
                'status' => LiveSessionStatus::Upcoming,
                'is_featured' => false,
                'target_tier' => LiveSessionTier::Vip,
            ]
        );

        // Recalculate counts for all sessions
        $session1->recalculateCounts();
        $session2->recalculateCounts();
        $session3->recalculateCounts();
        $session4->recalculateCounts();
    }
}
