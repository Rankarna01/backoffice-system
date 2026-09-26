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
        // 1. Seed FCS API (fcsapi.com) 1-Kolom API Configuration
        EconomicCalendarConfig::updateOrCreate(
            ['provider' => 'fcsapi'],
            [
                'name' => 'FCS API (fcsapi.com)',
                'api_key' => 'AHw1wEDTk4Vqzyf3ElPTT3', // User Access Key from fcsapi.com dashboard
                'base_url' => 'https://api-v4.fcsapi.com',
                'status' => 'connected',
                'last_tested_at' => now(),
                'last_error_message' => null,
                'is_active' => true,
            ]
        );

        // 2. Seed Macro Economic Calendar Events
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
                'source' => 'FCS API',
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
                'source' => 'FCS API',
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
                'source' => 'FCS API',
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
                'source' => 'FCS API',
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
                'source' => 'FCS API',
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
                'source' => 'FCS API',
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
                'source' => 'FCS API',
            ],
            [
                'country' => 'Canada',
                'currency' => 'CAD',
                'event_name' => 'Budget Balance',
                'impact_level' => 'low',
                'actual' => null,
                'forecast' => null,
                'previous' => '0.99',
                'unit' => 'C$',
                'event_date' => now()->startOfWeek()->addDays(4)->setTime(15, 0),
                'period' => 'Jul',
                'source' => 'FCS API',
            ],
            [
                'country' => 'United States',
                'currency' => 'USD',
                'event_name' => 'Michigan Consumer Sentiment',
                'impact_level' => 'medium',
                'actual' => '55.2',
                'forecast' => '45.8',
                'previous' => '51.5',
                'unit' => 'Points',
                'event_date' => now()->startOfWeek()->addDays(4)->setTime(14, 0),
                'period' => 'Sep',
                'source' => 'FCS API',
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
