<?php

namespace Tests\Feature;

use App\Domain\Learning\Models\Course;
use App\Domain\Learning\Models\Lesson;
use App\Domain\Learning\Models\Module;
use App\Filament\Resources\Modules\Pages\EditModule;
use App\Filament\Resources\Modules\RelationManagers\LessonsRelationManager;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LessonResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_admin_can_access_lessons_list_resource(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->get('/admin/lessons');
        $response->assertSuccessful();
        $response->assertSee('Mengapa 95% Retail Trader Merugi di Pasar Finansial');
        $response->assertSee('Video');
        $response->assertSee('Free Preview');
    }

    public function test_admin_can_access_lesson_create_page(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $response = $this->actingAs($admin)->get('/admin/lessons/create');
        $response->assertSuccessful();
        $response->assertSee('Informasi Materi & Penempatan');
        $response->assertSee('Konten Pelajaran & Media');
        $response->assertSee('Pengaturan Akses & Publikasi');
        $response->assertSee('Judul Pelajaran / Materi');
    }

    public function test_admin_can_access_lesson_create_page_with_module_query(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $module = Module::first();
        $this->assertNotNull($module);

        $response = $this->actingAs($admin)->get("/admin/lessons/create?module_id={$module->id}&course_id={$module->course_id}");
        $response->assertSuccessful();
        $response->assertSee('Informasi Materi & Penempatan');
    }

    public function test_admin_can_access_lesson_edit_page(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $lesson = Lesson::first();
        $this->assertNotNull($lesson);

        $response = $this->actingAs($admin)->get("/admin/lessons/{$lesson->id}/edit");
        $response->assertSuccessful();
        $response->assertSee($lesson->title);
        $response->assertSee('Informasi Materi & Penempatan');
    }

    public function test_mentor_can_access_lessons_resource(): void
    {
        $mentor = User::where('email', 'alex@tradingedu.com')->first();
        $this->assertNotNull($mentor);

        $response = $this->actingAs($mentor)->get('/admin/lessons');
        $response->assertSuccessful();
        $response->assertSee('Mengapa 95% Retail Trader Merugi di Pasar Finansial');
    }

    public function test_module_edit_page_contains_lessons_relation_manager(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $module = Module::first();
        $this->assertNotNull($module);

        $response = $this->actingAs($admin)->get("/admin/modules/{$module->id}/edit");
        $response->assertSuccessful();
        $response->assertSee('LessonsRelationManager');

        Livewire::actingAs($admin)
            ->test(LessonsRelationManager::class, [
                'ownerRecord' => $module,
                'pageClass' => EditModule::class,
            ])
            ->assertSuccessful()
            ->assertSee('Daftar Materi Pelajaran (Lessons)');
    }

    public function test_lesson_duration_formatting(): void
    {
        $lesson = Lesson::where('duration_seconds', 900)->first();
        $this->assertNotNull($lesson);
        $this->assertEquals('15:00', $lesson->duration_formatted);
    }

    public function test_lesson_soft_delete_and_restore(): void
    {
        $lesson = Lesson::first();
        $this->assertNotNull($lesson);

        $lesson->delete();
        $this->assertSoftDeleted('lessons', ['id' => $lesson->id]);

        $lesson->restore();
        $this->assertDatabaseHas('lessons', ['id' => $lesson->id, 'deleted_at' => null]);
    }
}
