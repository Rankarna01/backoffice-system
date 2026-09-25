<?php

namespace Tests\Feature;

use App\Domain\Learning\Models\Course;
use App\Domain\Learning\Models\Module;
use App\Domain\Learning\Models\Quiz;
use App\Filament\Resources\Courses\Pages\EditCourse;
use App\Filament\Resources\Courses\RelationManagers\QuizzesRelationManager as CourseQuizzesRelationManager;
use App\Filament\Resources\Modules\Pages\EditModule;
use App\Filament\Resources\Modules\RelationManagers\QuizzesRelationManager as ModuleQuizzesRelationManager;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class QuizResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_admin_can_access_quizzes_list_resource(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->get('/admin/quizzes');
        $response->assertSuccessful();
        $response->assertSee('Kuis Evaluasi: Market Structure, BOS & CHoCH');
        $response->assertSee('Passing Score');
        $response->assertSee('Syarat Kelulusan');
        $response->assertSee('Published');
    }

    public function test_admin_can_access_quiz_create_page(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $response = $this->actingAs($admin)->get('/admin/quizzes/create');
        $response->assertSuccessful();
        $response->assertSee('Informasi Kuis & Penempatan');
        $response->assertSee('Pertanyaan & Kunci Jawaban');
        $response->assertSee('Aturan Skor, Waktu & Publikasi');
        $response->assertSee('Judul Kuis Evaluasi');
    }

    public function test_admin_can_access_quiz_create_page_with_course_and_module_query(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $course = Course::first();
        $module = Module::first();
        $this->assertNotNull($course);
        $this->assertNotNull($module);

        $response = $this->actingAs($admin)->get("/admin/quizzes/create?course_id={$course->id}&module_id={$module->id}");
        $response->assertSuccessful();
        $response->assertSee('Informasi Kuis & Penempatan');
    }

    public function test_admin_can_access_quiz_edit_page(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $quiz = Quiz::first();
        $this->assertNotNull($quiz);

        $response = $this->actingAs($admin)->get("/admin/quizzes/{$quiz->id}/edit");
        $response->assertSuccessful();
        $response->assertSee($quiz->title);
        $response->assertSee('Pertanyaan & Kunci Jawaban');
    }

    public function test_mentor_can_access_quizzes_resource(): void
    {
        $mentor = User::where('email', 'alex@tradingedu.com')->first();
        $this->assertNotNull($mentor);

        $response = $this->actingAs($mentor)->get('/admin/quizzes');
        $response->assertSuccessful();
        $response->assertSee('Kuis Evaluasi: Market Structure, BOS & CHoCH');
    }

    public function test_course_edit_page_contains_quizzes_relation_manager(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $course = Course::first();
        $this->assertNotNull($course);

        $response = $this->actingAs($admin)->get("/admin/courses/{$course->id}/edit");
        $response->assertSuccessful();

        Livewire::actingAs($admin)
            ->test(CourseQuizzesRelationManager::class, [
                'ownerRecord' => $course,
                'pageClass' => EditCourse::class,
            ])
            ->assertSuccessful()
            ->assertSee('Kuis & Evaluasi Pemahaman');
    }

    public function test_module_edit_page_contains_quizzes_relation_manager(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $module = Module::whereHas('quizzes')->first();
        $this->assertNotNull($module);

        $response = $this->actingAs($admin)->get("/admin/modules/{$module->id}/edit");
        $response->assertSuccessful();

        Livewire::actingAs($admin)
            ->test(ModuleQuizzesRelationManager::class, [
                'ownerRecord' => $module,
                'pageClass' => EditModule::class,
            ])
            ->assertSuccessful()
            ->assertSee('Kuis / Evaluasi Modul');
    }

    public function test_quiz_has_questions_and_correct_options(): void
    {
        $quiz = Quiz::where('title', 'like', '%Market Structure%')->first();
        $this->assertNotNull($quiz);
        $this->assertGreaterThanOrEqual(1, $quiz->questions()->count());

        $firstQuestion = $quiz->questions()->first();
        $this->assertNotNull($firstQuestion);
        $this->assertGreaterThanOrEqual(2, $firstQuestion->options()->count());
        $this->assertTrue($firstQuestion->options()->where('is_correct', true)->exists());
    }

    public function test_quiz_soft_delete_and_restore(): void
    {
        $quiz = Quiz::first();
        $this->assertNotNull($quiz);

        $quiz->delete();
        $this->assertSoftDeleted('quizzes', ['id' => $quiz->id]);

        $quiz->restore();
        $this->assertDatabaseHas('quizzes', ['id' => $quiz->id, 'deleted_at' => null]);
    }
}
