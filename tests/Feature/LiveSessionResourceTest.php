<?php

namespace Tests\Feature;

use App\Domain\Community\Enums\LiveSessionPlatform;
use App\Domain\Community\Enums\LiveSessionStatus;
use App\Domain\Community\Enums\LiveSessionTier;
use App\Domain\Community\Models\LiveSession;
use App\Domain\Community\Models\LiveSessionRegistration;
use App\Domain\Identity\Models\MentorProfile;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LiveSessionResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_admin_can_access_live_sessions_list_resource(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->get('/admin/live-sessions');
        $response->assertSuccessful();
        $response->assertSee('Live Sessions');
        $response->assertSee('Live NFP Trading Room');
        $response->assertSee('Sesi Akan Datang');
    }

    public function test_admin_can_access_live_session_create_page(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $response = $this->actingAs($admin)->get('/admin/live-sessions/create');
        $response->assertSuccessful();
        $response->assertSee('Judul Sesi / Webinar');
        $response->assertSee('Platform Streaming');
        $response->assertSee('URL Ruangan / Link Meeting');
    }

    public function test_admin_can_access_live_session_edit_page(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $session = LiveSession::first();
        $this->assertNotNull($session);

        $response = $this->actingAs($admin)->get("/admin/live-sessions/{$session->id}/edit");
        $response->assertSuccessful();
        $response->assertSee($session->title);
    }

    public function test_mentor_can_access_live_session_resource(): void
    {
        $mentor = User::where('email', 'alex@tradingedu.com')->first();
        $this->assertNotNull($mentor);

        $response = $this->actingAs($mentor)->get('/admin/live-sessions');
        $response->assertSuccessful();
        $response->assertSee('Live Sessions');
    }

    public function test_registration_creation_and_attendance_updates_counts(): void
    {
        $session = LiveSession::where('status', LiveSessionStatus::Upcoming)->first();
        $this->assertNotNull($session);

        $initialRegistered = $session->registered_count;
        $initialAttended = $session->attended_count;

        $newUser = User::factory()->create();

        $registration = LiveSessionRegistration::create([
            'live_session_id' => $session->id,
            'user_id' => $newUser->id,
            'registered_at' => now(),
            'attended' => false,
        ]);

        $this->assertEquals($initialRegistered + 1, $session->fresh()->registered_count);
        $this->assertEquals($initialAttended, $session->fresh()->attended_count);

        $registration->update([
            'attended' => true,
            'attended_at' => now(),
        ]);

        $this->assertEquals($initialAttended + 1, $session->fresh()->attended_count);

        $registration->delete();
        $this->assertEquals($initialRegistered, $session->fresh()->registered_count);
        $this->assertEquals($initialAttended, $session->fresh()->attended_count);
    }

    public function test_live_session_can_transition_to_live_and_completed_with_replay(): void
    {
        $mentor = MentorProfile::first();
        $session = LiveSession::create([
            'mentor_id' => $mentor->id,
            'title' => 'Test Sesi Transisi Status',
            'scheduled_at' => now()->addHour(),
            'duration_minutes' => 60,
            'platform' => LiveSessionPlatform::Zoom,
            'join_url' => 'https://zoom.us/j/test-session-123',
            'status' => LiveSessionStatus::Upcoming,
            'target_tier' => LiveSessionTier::All,
        ]);

        $this->assertFalse($session->isLiveNow());

        // Mulai Live
        $session->update(['status' => LiveSessionStatus::Live]);
        $this->assertTrue($session->fresh()->isLiveNow());
        $this->assertTrue($session->fresh()->canJoinNow());

        // Selesai Sesi dengan Replay
        $session->update([
            'status' => LiveSessionStatus::Completed,
            'recording_url' => 'https://youtube.com/watch?v=replay_complete',
            'recording_duration_minutes' => 58,
        ]);

        $fresh = $session->fresh();
        $this->assertEquals(LiveSessionStatus::Completed, $fresh->status);
        $this->assertEquals('https://youtube.com/watch?v=replay_complete', $fresh->recording_url);
        $this->assertEquals(58, $fresh->recording_duration_minutes);
    }

    public function test_live_session_soft_delete(): void
    {
        $session = LiveSession::first();
        $id = $session->id;

        $session->delete();
        $this->assertSoftDeleted('live_sessions', ['id' => $id]);
    }
}
