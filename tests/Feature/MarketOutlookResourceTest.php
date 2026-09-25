<?php

namespace Tests\Feature;

use App\Domain\Market\Models\MarketOutlook;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketOutlookResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_admin_can_access_market_outlook_list_resource(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->get('/admin/market-outlooks');
        $response->assertSuccessful();
        $response->assertSee('Weekly Outlook: Emas (XAUUSD)');
        $response->assertSee('Forex Macro');
        $response->assertSee('Komoditas');
        $response->assertSee('Forex');
        $response->assertSee('Kripto');
    }

    public function test_admin_can_access_market_outlook_create_page(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $response = $this->actingAs($admin)->get('/admin/market-outlooks/create');
        $response->assertSuccessful();
        $response->assertSee('Informasi Utama & Makro');
        $response->assertSee('Konten Analisa & Chart');
        $response->assertSee('Key Takeaways & Key Levels');
        $response->assertSee('Judul Ulasan Market Outlook');
    }

    public function test_admin_can_access_market_outlook_edit_page(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $outlook = MarketOutlook::first();
        $this->assertNotNull($outlook);

        $response = $this->actingAs($admin)->get("/admin/market-outlooks/{$outlook->id}/edit");
        $response->assertSuccessful();
        $response->assertSee($outlook->title);
        $response->assertSee('Konten Analisa & Chart');
    }

    public function test_mentor_can_access_market_outlook_resource(): void
    {
        $mentor = User::where('email', 'alex@tradingedu.com')->first();
        $this->assertNotNull($mentor);

        $response = $this->actingAs($mentor)->get('/admin/market-outlooks');
        $response->assertSuccessful();
        $response->assertSee('Market Outlooks');
    }

    public function test_market_outlook_model_helpers(): void
    {
        $outlook = MarketOutlook::where('slug', 'weekly-outlook-emas-xauusd-mengincar-all-time-high-2700')->first();
        $this->assertNotNull($outlook);

        $this->assertEquals('success', $outlook->sentiment_color);
        $this->assertStringContainsString('Bullish', $outlook->sentiment_label);
        $this->assertIsArray($outlook->featured_pairs);
        $this->assertContains('XAUUSD', $outlook->featured_pairs);
        $this->assertIsArray($outlook->key_takeaways);
        $this->assertGreaterThan(0, count($outlook->key_takeaways));
        $this->assertIsArray($outlook->support_resistance_levels);
    }

    public function test_market_outlook_soft_delete_and_restore(): void
    {
        $outlook = MarketOutlook::first();
        $this->assertNotNull($outlook);

        $outlook->delete();
        $this->assertSoftDeleted('market_outlooks', ['id' => $outlook->id]);

        $outlook->restore();
        $this->assertDatabaseHas('market_outlooks', ['id' => $outlook->id, 'deleted_at' => null]);
    }
}
