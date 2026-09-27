<?php

namespace Database\Seeders;

use App\Domain\Notifications\Models\BroadcastNotification;
use App\Domain\Notifications\Models\NotificationLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $adminId = $admin?->id;

        $members = User::where('id', '!=', $adminId)->take(20)->get();

        $notifications = [
            [
                'title' => 'Webinar Eksklusif: Analisis Non-Farm Payrolls (NFP) Malam Ini',
                'body' => 'Siapkan diri Anda untuk Live Trading Session bersama Master Trader pukul 19:30 WIB. Kita akan membedah peluang setup Gold (XAUUSD) dan EURUSD menjelang rilis data ketenagakerjaan AS.',
                'type' => 'webinar',
                'target_audience' => 'all',
                'action_url' => '/live-sessions',
                'channels' => ['in_app', 'email'],
                'status' => 'sent',
                'scheduled_at' => now()->subDays(2)->setTime(18, 0),
                'sent_at' => now()->subDays(2)->setTime(18, 0),
            ],
            [
                'title' => 'Sinyal Presisi Terbit: Buy Limit XAUUSD (Area Demand H4)',
                'body' => 'Sinyal baru telah dirilis pada time frame H4 untuk pair XAUUSD dengan risk-to-reward 1:3. Cek area entry, stop loss, dan target profit di menu Signal.',
                'type' => 'signal',
                'target_audience' => 'subscribers',
                'action_url' => '/signals',
                'channels' => ['in_app', 'push'],
                'status' => 'sent',
                'scheduled_at' => now()->subDay()->setTime(10, 15),
                'sent_at' => now()->subDay()->setTime(10, 15),
            ],
            [
                'title' => 'Promo Spesial Kuartal: Diskon 30% Paket Pro Trader Membership',
                'body' => 'Gunakan kode kupon PROTRADER30 untuk mendapatkan potongan biaya berlangganan akses penuh komunitas VIP dan private outlook selama setahun.',
                'type' => 'promo',
                'target_audience' => 'free_members',
                'action_url' => '/pricing',
                'channels' => ['in_app', 'email'],
                'status' => 'sent',
                'scheduled_at' => now()->subDays(4)->setTime(9, 0),
                'sent_at' => now()->subDays(4)->setTime(9, 0),
            ],
            [
                'title' => 'Pembaruan Kurikulum: Modul Smart Money Concept (SMC) Bab 4 Telah Rilis',
                'body' => 'Video materi baru mengenai Order Flow and Liquidity Pools telah ditambahkan ke kursus Advanced Price Action. Silakan login ke dashboard kelas untuk menonton.',
                'type' => 'announcement',
                'target_audience' => 'course_students',
                'action_url' => '/my-courses',
                'channels' => ['in_app'],
                'status' => 'sent',
                'scheduled_at' => now()->subHours(8),
                'sent_at' => now()->subHours(8),
            ],
            [
                'title' => 'Jadwal Pemeliharaan Server Portal Edukasi (Maintenance Rutin)',
                'body' => 'Platform akan melakukan optimalisasi performa database pada hari Minggu dini hari pukul 01:00 - 03:00 WIB. Layanan streaming video mungkin mengalami jeda sesaat.',
                'type' => 'system',
                'target_audience' => 'all',
                'action_url' => null,
                'channels' => ['in_app', 'email'],
                'status' => 'scheduled',
                'scheduled_at' => now()->addDays(2)->setTime(1, 0),
                'sent_at' => null,
            ],
        ];

        foreach ($notifications as $data) {
            $notification = BroadcastNotification::create(array_merge($data, [
                'created_by' => $adminId,
                'total_recipients' => $data['status'] === 'sent' ? $members->count() : 0,
                'read_count' => $data['status'] === 'sent' ? rand(8, $members->count()) : 0,
            ]));

            if ($notification->status === 'sent') {
                $readLimit = $notification->read_count;
                $idx = 0;
                foreach ($members as $member) {
                    $isRead = ($idx < $readLimit);
                    NotificationLog::create([
                        'broadcast_id' => $notification->id,
                        'user_id' => $member->id,
                        'channel' => 'in_app',
                        'is_read' => $isRead,
                        'read_at' => $isRead ? now()->subHours(rand(1, 48)) : null,
                    ]);
                    $idx++;
                }
            }
        }
    }
}
