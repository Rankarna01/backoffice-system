<?php

namespace Tests\Feature;

use App\Domain\Analytics\Models\AnalyticsDailySnapshot;
use App\Domain\Notifications\Models\BroadcastNotification;
use App\Filament\Pages\AnalyticsPage;
use App\Filament\Pages\NotificationsPage;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class NotificationsAndAnalyticsPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_admin_can_access_notifications_page(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->get('/admin/notifications');
        $response->assertSuccessful();
        $response->assertSee('Pusat Notifikasi');
        $response->assertSee('Total Siaran Notifikasi');
    }

    public function test_admin_can_access_analytics_page(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->get('/admin/analytics');
        $response->assertSuccessful();
        $response->assertSee('Analitik &amp; Performa Platform', false);
        $response->assertSee('Total Omzet Penjualan');
        $response->assertSee('Distribusi Gateway Pembayaran');
    }

    public function test_admin_can_create_and_send_broadcast_notification(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $this->actingAs($admin);

        Livewire::test(NotificationsPage::class)
            ->set('new_title', 'Live Trading XAUUSD Segera Dimulai')
            ->set('new_body', 'Klik tautan untuk bergabung ke Zoom room sekarang.')
            ->set('new_type', 'webinar')
            ->set('new_target_audience', 'all')
            ->set('new_channels', ['in_app', 'email'])
            ->call('saveBroadcast', true);

        $broadcast = BroadcastNotification::where('title', 'Live Trading XAUUSD Segera Dimulai')->first();
        $this->assertNotNull($broadcast);
        $this->assertEquals('sent', $broadcast->status);
        $this->assertGreaterThan(0, $broadcast->total_recipients);
    }

    public function test_admin_can_save_draft_broadcast_notification(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $this->actingAs($admin);

        Livewire::test(NotificationsPage::class)
            ->set('new_title', 'Draf Promo Diskon Ramadhan')
            ->set('new_body', 'Diskon spesial kelas SMC.')
            ->set('new_type', 'promo')
            ->set('new_target_audience', 'free_members')
            ->call('saveBroadcast', false);

        $broadcast = BroadcastNotification::where('title', 'Draf Promo Diskon Ramadhan')->first();
        $this->assertNotNull($broadcast);
        $this->assertEquals('draft', $broadcast->status);
    }

    public function test_admin_can_cancel_scheduled_broadcast_notification(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $this->actingAs($admin);

        $scheduled = BroadcastNotification::where('status', 'scheduled')->first();
        $this->assertNotNull($scheduled);

        Livewire::test(NotificationsPage::class)
            ->call('cancelBroadcast', $scheduled->id);

        $scheduled->refresh();
        $this->assertEquals('cancelled', $scheduled->status);
    }

    public function test_admin_can_delete_broadcast_notification(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $this->actingAs($admin);

        $broadcast = BroadcastNotification::first();
        $this->assertNotNull($broadcast);

        Livewire::test(NotificationsPage::class)
            ->call('deleteBroadcast', $broadcast->id);

        $this->assertSoftDeleted('broadcast_notifications', ['id' => $broadcast->id]);
    }

    public function test_admin_can_refresh_analytics_snapshot(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $this->actingAs($admin);

        Livewire::test(AnalyticsPage::class)
            ->call('refreshSnapshots');

        $today = now()->toDateString();
        $snapshot = AnalyticsDailySnapshot::whereDate('snapshot_date', $today)->first();
        $this->assertNotNull($snapshot);
        $this->assertGreaterThan(0, $snapshot->gross_revenue);
    }

    public function test_admin_can_change_analytics_period_filter(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $this->actingAs($admin);

        Livewire::test(AnalyticsPage::class)
            ->set('period', '7_days')
            ->assertSet('period', '7_days')
            ->set('period', 'all_time')
            ->assertSet('period', 'all_time');
    }
}
