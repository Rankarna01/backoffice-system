<?php

namespace Tests\Feature;

use App\Domain\Learning\Models\Bookmark;
use App\Domain\Learning\Models\Course;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\CourseSeeder;
use Database\Seeders\MentorSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookmarkResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([
            RoleAndPermissionSeeder::class,
            UserSeeder::class,
            MentorSeeder::class,
            CategorySeeder::class,
            CourseSeeder::class,
        ]);
        $this->user = User::where('email', 'budi@tradingedu.com')->first() ?? User::first();
    }

    public function test_can_list_bookmarks(): void
    {
        $course = Course::first();

        Bookmark::create([
            'user_id' => $this->user->id,
            'bookmarkable_type' => 'course',
            'bookmarkable_id' => $course->id,
            'note' => 'Catatan penting belajar',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/bookmarks');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'bookmarkable_type',
                        'bookmarkable_id',
                        'note',
                        'details',
                    ],
                ],
            ]);
    }

    public function test_can_save_bookmark(): void
    {
        $course = Course::first();

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/bookmarks', [
                'bookmarkable_type' => 'course',
                'bookmarkable_id' => $course->id,
                'note' => 'Persiapan ujian akhir',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('bookmarks', [
            'user_id' => $this->user->id,
            'bookmarkable_type' => 'course',
            'bookmarkable_id' => $course->id,
        ]);
    }

    public function test_can_delete_bookmark(): void
    {
        $course = Course::first();

        $bookmark = Bookmark::create([
            'user_id' => $this->user->id,
            'bookmarkable_type' => 'course',
            'bookmarkable_id' => $course->id,
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/v1/bookmarks/{$bookmark->id}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('bookmarks', [
            'id' => $bookmark->id,
        ]);
    }
}
