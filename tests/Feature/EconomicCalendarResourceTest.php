<?php

namespace Tests\Feature;

use App\Domain\Market\Models\WidgetConfig;
use App\Models\User;
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
        $response->assertSee('Economic Calendar');
        $response->assertSee('Global Default');
        $response->assertSee('Dark');
    }

    public function test_admin_can_access_economic_calendar_create_page(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $response = $this->actingAs($admin)->get('/admin/economic-calendars/create');
        $response->assertSuccessful();
        $response->assertSee('Tema Warna');
        $response->assertSee('Lebar Widget');
        $response->assertSee('Filter Tingkat Dampak');
        $response->assertSee('Filter Mata Uang');
    }

    public function test_admin_can_access_economic_calendar_edit_page(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $config = WidgetConfig::first();
        $this->assertNotNull($config);

        $response = $this->actingAs($admin)->get("/admin/economic-calendars/{$config->id}/edit");
        $response->assertSuccessful();
        $response->assertSee('Tema Warna');
    }

    public function test_mentor_can_access_economic_calendar_resource(): void
    {
        $mentor = User::where('email', 'alex@tradingedu.com')->first();
        $this->assertNotNull($mentor);

        $response = $this->actingAs($mentor)->get('/admin/economic-calendars');
        $response->assertSuccessful();
        $response->assertSee('Economic Calendar');
    }

    public function test_get_global_economic_calendar_config_api(): void
    {
        $response = $this->getJson('/api/economic-calendar-config');
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'colorTheme',
            'isTransparent',
            'width',
            'height',
            'locale',
            'importanceFilter',
            'currencyFilter',
            'color_theme',
            'is_transparent',
            'importance_filter',
            'currencies',
            'is_active',
        ]);

        $this->assertEquals('dark', $response->json('colorTheme'));
        $this->assertEquals('dark', $response->json('color_theme'));
        $this->assertStringContainsString('USD', $response->json('currencyFilter'));
    }

    public function test_get_customer_specific_economic_calendar_config_api(): void
    {
        $student = User::where('email', 'budi@student.com')->first() ?: User::factory()->create();

        // Create customer-specific override
        WidgetConfig::create([
            'widget_type' => 'economic_calendar',
            'customer_id' => $student->id,
            'color_theme' => 'light',
            'width' => '90%',
            'height' => '700',
            'locale' => 'id_ID',
            'importance_filter' => '1', // High impact only
            'currencies' => ['USD', 'IDR'],
            'is_active' => true,
        ]);

        // Direct /api/customers/:id/economic-calendar-config endpoint
        $response = $this->getJson("/api/customers/{$student->id}/economic-calendar-config");
        $response->assertSuccessful();
        $this->assertEquals('light', $response->json('colorTheme'));
        $this->assertEquals('id_ID', $response->json('locale'));
        $this->assertEquals('1', $response->json('importanceFilter'));
        $this->assertEquals('USD,IDR', $response->json('currencyFilter'));
        $this->assertEquals($student->id, $response->json('customer_id'));
    }

    public function test_customer_without_override_receives_global_default_config(): void
    {
        $otherStudentId = 99999;

        $response = $this->getJson("/api/customers/{$otherStudentId}/economic-calendar-config");
        $response->assertSuccessful();
        $this->assertEquals('dark', $response->json('colorTheme'));
        $this->assertNull($response->json('customer_id'));
    }

    public function test_widget_config_model_and_trading_view_formatting(): void
    {
        $config = WidgetConfig::getEffectiveConfig();
        $this->assertNotNull($config);

        $tvConfig = $config->toTradingViewConfig();
        $this->assertIsArray($tvConfig);
        $this->assertArrayHasKey('colorTheme', $tvConfig);
        $this->assertArrayHasKey('isTransparent', $tvConfig);
        $this->assertArrayHasKey('width', $tvConfig);
        $this->assertArrayHasKey('height', $tvConfig);
        $this->assertArrayHasKey('importanceFilter', $tvConfig);
        $this->assertArrayHasKey('currencyFilter', $tvConfig);
    }
}
