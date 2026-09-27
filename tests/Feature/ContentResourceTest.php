<?php

namespace Tests\Feature;

use App\Domain\Content\Enums\AnnouncementStatus;
use App\Domain\Content\Enums\ReviewStatus;
use App\Domain\Content\Models\Announcement;
use App\Domain\Content\Models\Faq;
use App\Domain\Content\Models\Review;
use App\Domain\Content\Models\Testimonial;
use App\Domain\Media\Models\MediaAsset;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_admin_can_access_announcements_resource(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->get('/admin/announcements');
        $response->assertSuccessful();
        $response->assertSee('Announcements');
        $response->assertSee('Pengumuman Tayang');
    }

    public function test_admin_can_access_reviews_resource(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $response = $this->actingAs($admin)->get('/admin/reviews');
        $response->assertSuccessful();
        $response->assertSee('Reviews');
        $response->assertSee('Rata-rata Rating');
    }

    public function test_admin_can_access_testimonials_resource(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $response = $this->actingAs($admin)->get('/admin/testimonials');
        $response->assertSuccessful();
        $response->assertSee('Testimonials');
        $response->assertSee('Testimoni Tayang');
    }

    public function test_admin_can_access_faqs_resource(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $response = $this->actingAs($admin)->get('/admin/faqs');
        $response->assertSuccessful();
        $response->assertSee('FAQ');
        $response->assertSee('FAQ Publikasi Aktif');
    }

    public function test_admin_can_access_media_resource(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $response = $this->actingAs($admin)->get('/admin/media');
        $response->assertSuccessful();
        $response->assertSee('Media');
    }

    public function test_review_moderation_approval(): void
    {
        $review = Review::create([
            'user_id' => User::first()->id,
            'rating' => 4,
            'title' => 'Test Review Pending Moderation',
            'comment' => 'Materi sangat membantu dan jelas.',
            'status' => ReviewStatus::Pending,
        ]);

        $this->assertEquals(ReviewStatus::Pending, $review->status);

        $review->update([
            'status' => ReviewStatus::Approved,
            'approved_at' => now(),
        ]);

        $this->assertEquals(ReviewStatus::Approved, $review->fresh()->status);
        $this->assertNotNull($review->fresh()->approved_at);
    }

    public function test_announcement_published_scope(): void
    {
        $activeCount = Announcement::published()->count();
        $this->assertGreaterThan(0, $activeCount);

        $draft = Announcement::create([
            'title' => 'Draft Pengumuman Internal',
            'body' => 'Hanya untuk pengujian draft...',
            'status' => AnnouncementStatus::Draft,
        ]);

        $this->assertFalse(Announcement::published()->where('id', $draft->id)->exists());
    }

    public function test_faq_published_scope(): void
    {
        $faqs = Faq::published()->get();
        $this->assertNotEmpty($faqs);
        $this->assertTrue($faqs->every(fn ($faq) => $faq->is_published));
    }
}
