<?php

namespace Tests\Feature;

use Tests\TestCase;

class TradingToolTest extends TestCase
{
    public function test_can_fetch_trading_tools_rates(): void
    {
        $response = $this->getJson('/api/v1/trading/tools/rates');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'usd_to_idr',
                'data',
                'disclaimer',
            ]);
    }

    public function test_can_calculate_pip_value(): void
    {
        $response = $this->postJson('/api/v1/trading/tools/calculate-pip', [
            'pair' => 'EURUSD',
            'lot_size' => 1.0,
            'pips' => 20,
            'account_currency' => 'USD',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.pip_value', 10)
            ->assertJsonPath('data.total_value', 200);
    }

    public function test_can_calculate_lot_size(): void
    {
        $response = $this->postJson('/api/v1/trading/tools/calculate-lot', [
            'pair' => 'EURUSD',
            'account_balance' => 10000,
            'risk_percent' => 2.0,
            'sl_pips' => 50,
            'account_currency' => 'USD',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.risk_amount', 200)
            ->assertJsonPath('data.recommended_lot', 0.4);
    }

    public function test_can_calculate_risk_and_reward(): void
    {
        $response = $this->postJson('/api/v1/trading/tools/calculate-risk', [
            'pair' => 'EURUSD',
            'position_type' => 'buy',
            'entry_price' => 1.0850,
            'sl_price' => 1.0800,
            'tp_price' => 1.1000,
            'lot_size' => 1.0,
            'account_currency' => 'USD',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.rr_ratio', 3)
            ->assertJsonPath('data.sl_pips', 50)
            ->assertJsonPath('data.tp_pips', 150);
    }

    public function test_can_calculate_compounding(): void
    {
        $response = $this->postJson('/api/v1/trading/tools/calculate-compounding', [
            'principal' => 1000,
            'rate_percent' => 10,
            'periods' => 3,
            'period_type' => 'monthly',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.principal', 1000)
            ->assertJsonPath('data.final_balance', 1331);
    }
}
