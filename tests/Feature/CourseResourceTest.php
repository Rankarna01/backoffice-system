<?php

namespace Tests\Feature;

use App\Domain\Learning\Models\Category;
use App\Domain\Learning\Models\Course;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_admin_can_access_courses_list_resource(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->get('/admin/courses');
        $response->assertSuccessful();
        $response->assertSee('Smart Money Concept & Liquidity Mastery');
        $response->assertSee('Alex Wijaya');
        $response->assertSee('Price Action & Smart Money Concepts');
    }

    public function test_admin_can_access_course_create_page(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $response = $this->actingAs($admin)->get('/admin/courses/create');
        $response->assertSuccessful();
        $response->assertSee('Informasi Dasar');
        $response->assertSee('Model Akses & Harga');
    }

    public function test_admin_can_access_course_edit_page(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $course = Course::first();
        $this->assertNotNull($course);

        $response = $this->actingAs($admin)->get("/admin/courses/{$course->id}/edit");
        $response->assertSuccessful();
        $response->assertSee($course->title);
    }

    public function test_mentor_can_access_courses_resource(): void
    {
        $mentor = User::where('email', 'alex@tradingedu.com')->first();
        $this->assertNotNull($mentor);

        $response = $this->actingAs($mentor)->get('/admin/courses');
        $response->assertSuccessful();
    }

    public function test_course_lifecycle_status_transition(): void
    {
        $course = Course::where('slug', 'crypto-derivatives-order-flow')->first();
        $this->assertNotNull($course);
        $this->assertEquals('in_review', $course->status);

        // Admin approves and publishes course
        $course->update([
            'status' => 'published',
            'published_at' => now(),
        ]);

        $this->assertEquals('published', $course->fresh()->status);
        $this->assertNotNull($course->fresh()->published_at);
    }

    public function test_unauthenticated_user_redirected_from_courses(): void
    {
        $response = $this->get('/admin/courses');
        $response->assertRedirect('/admin/login');
    }

    public function test_customer_cannot_access_courses_resource(): void
    {
        $customer = User::where('email', 'customer@tradingedu.com')->first();
        $this->assertNotNull($customer);

        $response = $this->actingAs($customer)->get('/admin/courses');
        $response->assertForbidden();
    }
}
