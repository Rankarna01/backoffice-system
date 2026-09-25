<?php

namespace Tests\Feature;

use App\Domain\Market\Models\MarketNews;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketNewsResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_admin_can_access_market_news_list_resource(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->get('/admin/market-news');
        $response->assertSuccessful();
        $response->assertSee('The Fed Pangkas Suku Bunga');
        $response->assertSee('Emas Cetak Rekor');
        $response->assertSee('High Impact');
    }

    public function test_admin_can_access_market_news_create_page(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $response = $this->actingAs($admin)->get('/admin/market-news/create');
        $response->assertSuccessful();
        $response->assertSee('Headline & Parameter Berita');
        $response->assertSee('Isi Berita & Media Visual');
        $response->assertSee('Judul Berita Pasar Finansial');
    }

    public function test_admin_can_access_market_news_edit_page(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $news = MarketNews::first();
        $this->assertNotNull($news);

        $response = $this->actingAs($admin)->get("/admin/market-news/{$news->id}/edit");
        $response->assertSuccessful();
        $response->assertSee($news->title);
        $response->assertSee('Headline & Parameter Berita');
    }

    public function test_mentor_can_access_market_news_resource(): void
    {
        $mentor = User::where('email', 'alex@tradingedu.com')->first();
        $this->assertNotNull($mentor);

        $response = $this->actingAs($mentor)->get('/admin/market-news');
        $response->assertSuccessful();
        $response->assertSee('Market News');
    }

    public function test_market_news_model_helpers(): void
    {
        $news = MarketNews::where('is_breaking', true)->first();
        $this->assertNotNull($news);

        $this->assertEquals('danger', $news->impact_color);
        $this->assertStringContainsString('High Impact', $news->impact_label);
        $this->assertIsArray($news->related_symbols);
        $this->assertTrue($news->is_breaking);
    }

    public function test_market_news_soft_delete_and_restore(): void
    {
        $news = MarketNews::first();
        $this->assertNotNull($news);

        $news->delete();
        $this->assertSoftDeleted('market_news', ['id' => $news->id]);

        $news->restore();
        $this->assertDatabaseHas('market_news', ['id' => $news->id, 'deleted_at' => null]);
    }
}
