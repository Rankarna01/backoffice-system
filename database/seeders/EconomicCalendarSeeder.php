<?php

namespace Database\Seeders;

use App\Domain\Market\Models\EconomicCalendarConfig;
use App\Domain\Market\Models\EconomicCalendarEvent;
use Illuminate\Database\Seeder;

class EconomicCalendarSeeder extends Seeder
{
    /**
     * Run the database seeds for Economic Calendar configuration and events.
     */
    public function run(): void
    {
        // 1. Seed Trading Economics 1-Kolom API Configuration
        EconomicCalendarConfig::updateOrCreate(
            ['provider' => 'trading_economics'],
            [
                'name' => 'Trading Economics API (Kalender Ekonomi)',
                'api_key' => 'guest:guest', // Default demo key, dapat diubah sewaktu-waktu oleh user
                'base_url' => 'https://api.tradingeconomics.com',
                'status' => 'connected',
                'last_tested_at' => now(),
                'last_error_message' => null,
                'is_active' => true,
            ]
        );

        // 2. Seed Realistic Macro Economic Calendar Events
        $events = [
            [
                'country' => 'United States',
                'currency' => 'USD',
                'event_name' => 'Non Farm Payrolls (NFP)',
                'impact_level' => 'high',
                'actual' => '254K',
                'forecast' => '140K',
                'previous' => '159K',
                'unit' => 'K',
                'event_date' => now()->startOfWeek()->addDays(4)->setTime(19, 30),
                'period' => 'Sep',
                'source' => 'Trading Economics',
            ],
            [
                'country' => 'United States',
                'currency' => 'USD',
                'event_name' => 'Unemployment Rate',
                'impact_level' => 'high',
                'actual' => '4.1%',
                'forecast' => '4.2%',
                'previous' => '4.2%',
                'unit' => '%',
                'event_date' => now()->startOfWeek()->addDays(4)->setTime(19, 30),
                'period' => 'Sep',
                'source' => 'Trading Economics',
            ],
            [
                'country' => 'United States',
                'currency' => 'USD',
                'event_name' => 'Core Inflation Rate YoY (CPI)',
                'impact_level' => 'high',
                'actual' => '3.3%',
                'forecast' => '3.2%',
                'previous' => '3.2%',
                'unit' => '%',
                'event_date' => now()->startOfWeek()->addDays(2)->setTime(19, 30),
                'period' => 'Sep',
                'source' => 'Trading Economics',
            ],
            [
                'country' => 'United States',
                'currency' => 'USD',
                'event_name' => 'Fed Interest Rate Decision (FOMC)',
                'impact_level' => 'high',
                'actual' => '5.00%',
                'forecast' => '5.00%',
                'previous' => '5.50%',
                'unit' => '%',
                'event_date' => now()->startOfWeek()->addDays(1)->setTime(1, 0),
                'period' => 'Sep',
                'source' => 'Trading Economics',
            ],
            [
                'country' => 'Euro Area',
                'currency' => 'EUR',
                'event_name' => 'ECB Main Refinancing Rate Decision',
                'impact_level' => 'high',
                'actual' => '3.50%',
                'forecast' => '3.50%',
                'previous' => '3.75%',
                'unit' => '%',
                'event_date' => now()->startOfWeek()->addDays(3)->setTime(19, 15),
                'period' => 'Okt',
                'source' => 'Trading Economics',
            ],
            [
                'country' => 'United Kingdom',
                'currency' => 'GBP',
                'event_name' => 'BOE Official Bank Rate Decision',
                'impact_level' => 'high',
                'actual' => '5.00%',
                'forecast' => '5.00%',
                'previous' => '5.00%',
                'unit' => '%',
                'event_date' => now()->startOfWeek()->addDays(3)->setTime(18, 0),
                'period' => 'Okt',
                'source' => 'Trading Economics',
            ],
            [
                'country' => 'Japan',
                'currency' => 'JPY',
                'event_name' => 'BOJ Policy Rate & Statement',
                'impact_level' => 'high',
                'actual' => '0.25%',
                'forecast' => '0.25%',
                'previous' => '0.10%',
                'unit' => '%',
                'event_date' => now()->startOfWeek()->addDays(4)->setTime(10, 30),
                'period' => 'Okt',
                'source' => 'Trading Economics',
            ],
            [
                'country' => 'United States',
                'currency' => 'USD',
                'event_name' => 'GDP Growth Rate QoQ Final',
                'impact_level' => 'medium',
                'actual' => '3.0%',
                'forecast' => '2.9%',
                'previous' => '1.4%',
                'unit' => '%',
                'event_date' => now()->startOfWeek()->addDays(2)->setTime(19, 30),
                'period' => 'Q2',
                'source' => 'Trading Economics',
            ],
            [
                'country' => 'Euro Area',
                'currency' => 'EUR',
                'event_name' => 'HCOB Manufacturing PMI Flash',
                'impact_level' => 'medium',
                'actual' => '45.0',
                'forecast' => '45.6',
                'previous' => '45.8',
                'unit' => 'Points',
                'event_date' => now()->startOfWeek()->addDays(1)->setTime(15, 0),
                'period' => 'Sep',
                'source' => 'Trading Economics',
            ],
        ];

        foreach ($events as $event) {
            EconomicCalendarEvent::updateOrCreate(
                [
                    'country' => $event['country'],
                    'event_name' => $event['event_name'],
                    'period' => $event['period'],
                ],
                $event
            );
        }
    }
}
