<?php

namespace Tests\Feature;

use App\Domain\Market\Models\Signal;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SignalResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_admin_can_access_signals_list_resource(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->get('/admin/signals');
        $response->assertSuccessful();
        $response->assertSee('XAUUSD');
        $response->assertSee('EURUSD');
        $response->assertSee('BTCUSDT');
        $response->assertSee('US30');
        $response->assertSee('Komoditas');
        $response->assertSee('Forex');
        $response->assertSee('Kripto');
        $response->assertSee('Indeks');
    }

    public function test_admin_can_access_signal_create_page(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $response = $this->actingAs($admin)->get('/admin/signals/create');
        $response->assertSuccessful();
        $response->assertSee('Setup Market & Instrumen');
        $response->assertSee('Parameter Harga & R:R');
        $response->assertSee('Status & Analisa Chart');
        $response->assertSee('Judul Setup Sinyal');
    }

    public function test_admin_can_access_signal_edit_page(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $signal = Signal::first();
        $this->assertNotNull($signal);

        $response = $this->actingAs($admin)->get("/admin/signals/{$signal->id}/edit");
        $response->assertSuccessful();
        $response->assertSee($signal->title);
        $response->assertSee('Parameter Harga & R:R');
    }

    public function test_mentor_can_access_signals_resource(): void
    {
        $mentor = User::where('email', 'alex@tradingedu.com')->first();
        $this->assertNotNull($mentor);

        $response = $this->actingAs($mentor)->get('/admin/signals');
        $response->assertSuccessful();
        $response->assertSee('Sinyal Trading');
    }

    public function test_risk_reward_computation(): void
    {
        // BUY: Entry 2000, SL 1990 (Risk 10), TP 2030 (Reward 30) -> 1:3.0
        $rrBuy = Signal::computeRiskReward('BUY', 2000, 1990, 2030);
        $this->assertEquals('1:3', $rrBuy);

        // SELL: Entry 1.1000, SL 1.1020 (Risk 0.0020), TP 1.0940 (Reward 0.0060) -> 1:3.0
        $rrSell = Signal::computeRiskReward('SELL', 1.1000, 1.1020, 1.0940);
        $this->assertEquals('1:3', $rrSell);
    }

    public function test_signal_status_and_pips_update(): void
    {
        $signal = Signal::where('pair', 'XAUUSD')->where('status', 'active')->first();
        $this->assertNotNull($signal);

        $signal->update([
            'status' => 'hit_tp1',
            'result_pips' => 150.0,
            'closed_at' => now(),
        ]);

        $this->assertEquals('hit_tp1', $signal->fresh()->status);
        $this->assertEquals(150.0, $signal->fresh()->result_pips);
        $this->assertNotNull($signal->fresh()->closed_at);
    }

    public function test_signal_soft_delete_and_restore(): void
    {
        $signal = Signal::first();
        $this->assertNotNull($signal);

        $signal->delete();
        $this->assertSoftDeleted('signals', ['id' => $signal->id]);

        $signal->restore();
        $this->assertDatabaseHas('signals', ['id' => $signal->id, 'deleted_at' => null]);
    }
}
