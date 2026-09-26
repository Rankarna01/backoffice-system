<?php

namespace Tests\Feature;

use App\Domain\Market\Models\EconomicCalendarConfig;
use App\Domain\Market\Models\EconomicCalendarEvent;
use App\Models\User;
use App\Services\FcsApiService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class EconomicCalendarResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_admin_can_access_economic_calendar_list_resource(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->get('/admin/economic-calendars');
        $response->assertSuccessful();
        $response->assertSee('Non Farm Payrolls');
        $response->assertSee('USD');
        $response->assertSee('EUR');
        $response->assertSee('High 🔴');
    }

    public function test_admin_can_access_economic_calendar_create_page(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $response = $this->actingAs($admin)->get('/admin/economic-calendars/create');
        $response->assertSuccessful();
        $response->assertSee('Nama Indikator / Peristiwa Rilis');
        $response->assertSee('Mata Uang Terdampak (Currency)');
        $response->assertSee('Tingkat Dampak Volatilitas');
    }

    public function test_admin_can_access_economic_calendar_edit_page(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $event = EconomicCalendarEvent::first();
        $this->assertNotNull($event);

        $response = $this->actingAs($admin)->get("/admin/economic-calendars/{$event->id}/edit");
        $response->assertSuccessful();
        $response->assertSee($event->event_name);
    }

    public function test_mentor_can_access_economic_calendar_resource(): void
    {
        $mentor = User::where('email', 'alex@tradingedu.com')->first();
        $this->assertNotNull($mentor);

        $response = $this->actingAs($mentor)->get('/admin/economic-calendars');
        $response->assertSuccessful();
        $response->assertSee('Economic Calendar');
    }

    public function test_fcs_api_service_test_connection_success(): void
    {
        Http::fake([
            'https://api-v4.fcsapi.com/*' => Http::response([
                'status' => true,
                'code' => 200,
                'msg' => 'Successfully',
                'response' => [
                    [
                        'id' => '1001',
                        'title' => 'Consumer Price Index YoY',
                        'country' => 'US',
                        'currency' => 'USD',
                        'importance' => '3',
                        'actual' => '3.1%',
                        'forecast' => '3.1%',
                        'previous' => '3.2%',
                        'date' => '2026-09-26 12:30:00',
                    ],
                ],
                'info' => [
                    'credit_count' => 1,
                    'server_time' => '2026-09-26 14:00:00 UTC',
                ],
            ], 200),
        ]);

        /** @var FcsApiService $service */
        $service = app(FcsApiService::class);
        $result = $service->testConnection('AHw1wEDTk4Vqzyf3ElPTT3');

        $this->assertIsArray($result);
        $this->assertTrue($result['success']);
        $this->assertEquals(200, $result['status_code']);
        $this->assertEquals(1, $result['credit_count']);
        $this->assertNotEmpty($result['sample_data']);
    }

    public function test_fcs_api_service_sync_events(): void
    {
        Http::fake([
            'https://api-v4.fcsapi.com/*' => Http::response([
                'status' => true,
                'code' => 200,
                'response' => [
                    [
                        'id' => '2001',
                        'title' => 'Gross Domestic Product QoQ',
                        'indicator' => 'GDP Growth Rate',
                        'country' => 'US',
                        'currency' => 'USD',
                        'importance' => '3',
                        'actual' => '2.8%',
                        'forecast' => '3.0%',
                        'previous' => '1.6%',
                        'unit' => '%',
                        'period' => 'Q2',
                        'source' => 'Bureau of Economic Analysis',
                        'date' => '2026-09-26 13:30:00',
                    ],
                ],
            ], 200),
        ]);

        /** @var FcsApiService $service */
        $service = app(FcsApiService::class);
        $synced = $service->syncEvents();

        $this->assertGreaterThan(0, $synced);
        $this->assertDatabaseHas('economic_calendar_events', [
            'event_name' => 'Gross Domestic Product QoQ',
            'currency' => 'USD',
            'impact_level' => 'high',
        ]);
    }

    public function test_economic_calendar_config_model_and_key_storage(): void
    {
        $config = EconomicCalendarConfig::where('provider', 'fcsapi')->first();
        $this->assertNotNull($config);
        $this->assertEquals('fcsapi', $config->provider);
        $this->assertEquals('AHw1wEDTk4Vqzyf3ElPTT3', $config->api_key);

        // Update key with custom user key
        $config->update(['api_key' => 'new_custom_fcs_key_9999']);
        $this->assertEquals('new_custom_fcs_key_9999', $config->fresh()->api_key);
    }

    public function test_economic_calendar_event_delete(): void
    {
        $event = EconomicCalendarEvent::first();
        $this->assertNotNull($event);

        $id = $event->id;
        $event->delete();

        $this->assertDatabaseMissing('economic_calendar_events', ['id' => $id]);
    }
}
