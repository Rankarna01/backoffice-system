<?php

namespace Tests\Feature;

use App\Domain\Learning\Models\Course;
use App\Domain\Learning\Models\Module;
use App\Filament\Resources\Courses\Pages\EditCourse;
use App\Filament\Resources\Courses\RelationManagers\ModulesRelationManager;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ModuleResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_admin_can_access_modules_list_resource(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->get('/admin/modules');
        $response->assertSuccessful();
        $response->assertSee('Bab 1: Fondasi & Logika Pasar Smart Money');
        $response->assertSee('Smart Money Concept & Liquidity Mastery');
    }

    public function test_admin_can_access_module_create_page(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $response = $this->actingAs($admin)->get('/admin/modules/create');
        $response->assertSuccessful();
        $response->assertSee('Informasi Dasar & Silabus');
        $response->assertSee('Pengaturan & Publikasi');
        $response->assertSee('Kursus Induk');
        $response->assertSee('Judul Modul / Bab');
    }

    public function test_admin_can_access_module_create_page_with_course_query(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $course = Course::first();
        $response = $this->actingAs($admin)->get("/admin/modules/create?course_id={$course->id}");
        $response->assertSuccessful();
        $response->assertSee('Informasi Dasar & Silabus');
        $response->assertSee('Smart Money Concept');
    }

    public function test_admin_can_access_module_edit_page(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $module = Module::first();
        $this->assertNotNull($module);

        $response = $this->actingAs($admin)->get("/admin/modules/{$module->id}/edit");
        $response->assertSuccessful();
        $response->assertSee($module->title);
        $response->assertSee('Informasi Dasar & Silabus');
        $response->assertSee('Pengaturan & Publikasi');
    }

    public function test_mentor_can_access_modules_resource(): void
    {
        $mentor = User::where('email', 'alex@tradingedu.com')->first();
        $this->assertNotNull($mentor);

        $response = $this->actingAs($mentor)->get('/admin/modules');
        $response->assertSuccessful();
        $response->assertSee('Bab 1: Fondasi & Logika Pasar Smart Money');
    }

    public function test_course_edit_page_contains_modules_relation_manager(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $course = Course::where('slug', 'smart-money-concepts-mastery')->first();
        $this->assertNotNull($course);

        $response = $this->actingAs($admin)->get("/admin/courses/{$course->id}/edit");
        $response->assertSuccessful();
        $response->assertSee('ModulesRelationManager');

        Livewire::actingAs($admin)
            ->test(ModulesRelationManager::class, [
                'ownerRecord' => $course,
                'pageClass' => EditCourse::class,
            ])
            ->assertSuccessful()
            ->assertSee('Silabus Bab & Modul Kursus')
            ->assertSee('Bab 1: Fondasi & Logika Pasar Smart Money');
    }

    public function test_module_soft_delete_and_restore(): void
    {
        $module = Module::first();
        $this->assertNotNull($module);

        $module->delete();
        $this->assertSoftDeleted('modules', ['id' => $module->id]);

        $module->restore();
        $this->assertDatabaseHas('modules', ['id' => $module->id, 'deleted_at' => null]);
    }
}
