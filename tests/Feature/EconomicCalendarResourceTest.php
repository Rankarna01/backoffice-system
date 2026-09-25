<?php

namespace Tests\Feature;

use App\Domain\Market\Models\EconomicCalendarConfig;
use App\Domain\Market\Models\EconomicCalendarEvent;
use App\Models\User;
use App\Services\TradingEconomicsService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    public function test_trading_economics_service_test_connection(): void
    {
        /** @var TradingEconomicsService $service */
        $service = app(TradingEconomicsService::class);
        $result = $service->testConnection('guest:guest');

        $this->assertIsArray($result);
        $this->assertTrue($result['success']);
        $this->assertTrue($result['is_demo']);
        $this->assertNotEmpty($result['message']);
    }

    public function test_economic_calendar_config_model_and_key_storage(): void
    {
        $config = EconomicCalendarConfig::first();
        $this->assertNotNull($config);
        $this->assertEquals('trading_economics', $config->provider);
        $this->assertNotEmpty($config->api_key);
        $this->assertTrue($config->is_demo);

        // Update key with custom user key
        $config->update(['api_key' => 'custom_user_te_key_12345']);
        $this->assertFalse($config->fresh()->is_demo);
        $this->assertEquals('custom_user_te_key_12345', $config->fresh()->api_key);
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
