<?php

namespace Tests\Feature;

use App\Domain\Trading\Models\JournalTrade;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TradingJournalResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->user = User::where('email', 'budi@tradingedu.com')->first() ?? User::first();
    }

    public function test_can_list_trading_journals_for_user(): void
    {
        JournalTrade::create([
            'user_id' => $this->user->id,
            'entry_time' => now(),
            'pair' => 'XAU/USD',
            'type' => 'long',
            'status' => 'win',
            'currency' => 'USD',
            'risk_amount' => 100,
            'profit_loss' => 250,
            'setup' => 'SnR Breakout',
            'is_disciplined' => true,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/trading/journal');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'uuid',
                        'user_id',
                        'entry_time',
                        'pair',
                        'type',
                        'status',
                        'profit_loss',
                    ],
                ],
                'meta' => [
                    'current_page',
                    'total',
                ],
            ]);
    }

    public function test_can_get_trading_journal_stats(): void
    {
        JournalTrade::create([
            'user_id' => $this->user->id,
            'entry_time' => now(),
            'pair' => 'XAU/USD',
            'type' => 'long',
            'status' => 'win',
            'currency' => 'USD',
            'risk_amount' => 100,
            'profit_loss' => 200,
            'is_disciplined' => true,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/trading/journal/stats');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'total_trades',
                    'win_trades',
                    'loss_trades',
                    'win_rate',
                    'total_pnl',
                ],
            ]);
    }

    public function test_can_create_new_journal_trade(): void
    {
        $payload = [
            'entry_time' => now()->toIso8601String(),
            'pair' => 'GBP/JPY',
            'type' => 'long',
            'status' => 'open',
            'currency' => 'USD',
            'risk_amount' => 150,
            'profit_loss' => 0,
            'setup' => 'London Breakout',
            'emotion' => 'Tenang & Disiplin',
            'session' => 'Sesi London (Eropa)',
            'is_disciplined' => true,
            'notes' => 'Setup sesuai plan harian.',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/trading/journal', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.pair', 'GBP/JPY')
            ->assertJsonPath('data.type', 'long')
            ->assertJsonPath('data.status', 'open');

        $this->assertDatabaseHas('journal_trades', [
            'user_id' => $this->user->id,
            'pair' => 'GBP/JPY',
            'status' => 'open',
        ]);
    }

    public function test_can_delete_own_journal_trade(): void
    {
        $trade = JournalTrade::create([
            'user_id' => $this->user->id,
            'entry_time' => now(),
            'pair' => 'BTC/USDT',
            'type' => 'long',
            'status' => 'open',
            'currency' => 'USD',
            'risk_amount' => 50,
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/v1/trading/journal/{$trade->id}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertSoftDeleted('journal_trades', [
            'id' => $trade->id,
        ]);
    }
}
