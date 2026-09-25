<?php

namespace Tests\Feature;

use App\Domain\Media\Models\MediaAsset;
use App\Models\User;
use App\Services\CloudflareR2Service;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MediaResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_admin_can_access_media_resource_list(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->get('/admin/media');
        $response->assertSuccessful();
        $response->assertSee('Smart Money Concepts Mastery');
        $response->assertSee('Cloudflare R2');
        $response->assertSee('courses/thumbnails');
        $response->assertSee('Video Materi');
    }

    public function test_admin_can_access_media_create_page(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $response = $this->actingAs($admin)->get('/admin/media/create');
        $response->assertSuccessful();
        $response->assertSee('Upload & Penempatan R2');
        $response->assertSee('Judul / Label Aset');
        $response->assertSee('Kategori / Folder Penyimpanan');
    }

    public function test_admin_can_access_media_edit_page(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $asset = MediaAsset::first();
        $this->assertNotNull($asset);

        $response = $this->actingAs($admin)->get("/admin/media/{$asset->id}/edit");
        $response->assertSuccessful();
        $response->assertSee($asset->name);
        $response->assertSee('Path Object di Bucket R2');
        $response->assertSee('URL CDN Publik Edge');
    }

    public function test_mentor_can_access_media_resource(): void
    {
        $mentor = User::where('email', 'alex@tradingedu.com')->first();
        $this->assertNotNull($mentor);

        $response = $this->actingAs($mentor)->get('/admin/media');
        $response->assertSuccessful();
        $response->assertSee('Katalog Media Cloudflare R2');
    }

    public function test_cloudflare_r2_service_returns_valid_configuration(): void
    {
        /** @var CloudflareR2Service $r2 */
        $r2 = app(CloudflareR2Service::class);
        $status = $r2->getConnectionStatus();

        $this->assertEquals('course-trading-media', $status['bucket']);
        $this->assertTrue($status['s3_compatible']);
        $this->assertTrue($status['zero_egress_fee']);

        $usage = $r2->getStorageUsageSummary();
        $this->assertGreaterThan(0, $usage['total_bytes']);
        $this->assertGreaterThanOrEqual(10, $usage['total_files']);
    }

    public function test_media_asset_model_helpers(): void
    {
        $videoAsset = MediaAsset::where('type', 'video')->first();
        $this->assertNotNull($videoAsset);
        $this->assertEquals('danger', $videoAsset->type_badge_color);
        $this->assertStringContainsString('MB', $videoAsset->size_formatted);
        $this->assertStringContainsString('https://pub-r2.tradingedu.dev', $videoAsset->r2_cdn_url);
    }

    public function test_media_soft_delete_and_restore(): void
    {
        $asset = MediaAsset::first();
        $this->assertNotNull($asset);

        $asset->delete();
        $this->assertSoftDeleted('media_assets', ['id' => $asset->id]);

        $asset->restore();
        $this->assertDatabaseHas('media_assets', ['id' => $asset->id, 'deleted_at' => null]);
    }
}
