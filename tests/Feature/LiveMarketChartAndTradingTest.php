<?php

namespace Tests\Feature;

use App\Domain\Trading\Models\LiveChartConfig;
use App\Domain\Trading\Models\PaperTradingAccount;
use App\Domain\Trading\Models\PaperTradingPosition;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LiveMarketChartAndTradingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_admin_can_access_live_market_charts_resource_in_backoffice(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->get('/admin/live-market-charts');
        $response->assertSuccessful();
        $response->assertSee('Live Chart &amp; Akun Trading', false);
        $response->assertSee('Live Chart Embed (XAU/USD)');
    }

    public function test_api_can_fetch_live_chart_config(): void
    {
        $response = $this->getJson('/api/v1/market/chart/config');
        $response->assertSuccessful();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.default_symbol', 'OANDA:XAUUSD');
        $response->assertJsonPath('data.allow_trading', true);
        $response->assertJsonStructure([
            'data' => [
                'default_symbol',
                'default_interval',
                'theme',
                'watchlist' => [
                    'crypto',
                    'forex_gold',
                ],
                'default_balance',
            ],
        ]);
    }

    public function test_student_can_fetch_trading_account_and_auto_generate_demo_balance(): void
    {
        $student = User::factory()->create();

        $response = $this->actingAs($student, 'sanctum')->getJson('/api/v1/market/trading/account');
        $response->assertSuccessful();
        $response->assertJsonPath('success', true);
        $this->assertEquals(10000.00, $response->json('data.balance'));
        $this->assertEquals(10000.00, $response->json('data.equity'));
        $response->assertJsonPath('data.currency', 'USD');
    }

    public function test_student_can_open_and_close_trading_position(): void
    {
        $student = User::factory()->create();
        $account = PaperTradingAccount::getOrCreateForUser($student, 10000.00);

        // 1. Open BUY Order on XAU/USD
        $orderResponse = $this->actingAs($student, 'sanctum')->postJson('/api/v1/market/trading/order', [
            'symbol' => 'XAU/USD',
            'side' => 'buy',
            'units' => 1.0,
            'entry_price' => 4185.00,
            'stop_loss' => 4160.00,
            'take_profit' => 4220.00,
        ]);

        $orderResponse->assertCreated();
        $orderResponse->assertJsonPath('success', true);
        $positionId = $orderResponse->json('data.position_id');

        $this->assertDatabaseHas('paper_trading_positions', [
            'id' => $positionId,
            'symbol' => 'XAU/USD',
            'side' => 'buy',
            'status' => 'open',
        ]);

        // 2. Fetch positions
        $positionsResponse = $this->actingAs($student, 'sanctum')->getJson('/api/v1/market/trading/positions');
        $positionsResponse->assertSuccessful();
        $this->assertCount(1, $positionsResponse->json('data.open_positions'));

        // 3. Close position at higher price (Profit +$15.00)
        $closeResponse = $this->actingAs($student, 'sanctum')->postJson("/api/v1/market/trading/positions/{$positionId}/close", [
            'exit_price' => 4200.00,
        ]);

        $closeResponse->assertSuccessful();
        $closeResponse->assertJsonPath('success', true);
        $this->assertEquals(15.00, $closeResponse->json('data.realized_pnl'));
        $this->assertEquals(10015.00, $closeResponse->json('data.new_balance'));

        $account->refresh();
        $this->assertEquals(10015.00, (float) $account->balance);
    }

    public function test_student_can_reset_trading_account_back_to_ten_thousand(): void
    {
        $student = User::factory()->create();
        $account = PaperTradingAccount::getOrCreateForUser($student, 5000.00); // Loss account

        $response = $this->actingAs($student, 'sanctum')->postJson('/api/v1/market/trading/account/reset');
        $response->assertSuccessful();
        $this->assertEquals(10000.00, $response->json('data.balance'));

        $account->refresh();
        $this->assertEquals(10000.00, (float) $account->balance);
    }

    public function test_admin_can_set_and_modify_student_balance_directly_from_model(): void
    {
        $student = User::factory()->create();
        $account = PaperTradingAccount::getOrCreateForUser($student, 10000.00);

        // Admin sets balance to $25,000.00
        $account->balance = 25000.00;
        $account->notes = 'Bonus kompetisi trading SMC';
        $account->recalculateEquity();

        $account->refresh();
        $this->assertEquals(25000.00, (float) $account->balance);
        $this->assertEquals(25000.00, (float) $account->equity);
    }
}
