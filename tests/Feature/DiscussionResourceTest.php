<?php

namespace Tests\Feature;

use App\Domain\Community\Enums\DiscussionCategory;
use App\Domain\Community\Enums\DiscussionStatus;
use App\Domain\Community\Enums\ModerationStatus;
use App\Domain\Community\Models\Discussion;
use App\Domain\Community\Models\DiscussionReply;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiscussionResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_admin_can_access_discussions_list_resource(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->get('/admin/discussions');
        $response->assertSuccessful();
        $response->assertSee('Discussions');
        $response->assertSee('Analisis Breakout XAUUSD');
        $response->assertSee('Total Thread Diskusi');
    }

    public function test_admin_can_access_discussion_create_page(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $response = $this->actingAs($admin)->get('/admin/discussions/create');
        $response->assertSuccessful();
        $response->assertSee('Judul Diskusi');
        $response->assertSee('Kategori Forum');
        $response->assertSee('Isi Postingan / Pertanyaan');
    }

    public function test_admin_can_access_discussion_edit_page(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $discussion = Discussion::first();
        $this->assertNotNull($discussion);

        $response = $this->actingAs($admin)->get("/admin/discussions/{$discussion->id}/edit");
        $response->assertSuccessful();
        $response->assertSee($discussion->title);
        $response->assertSee('RepliesRelationManager');
    }

    public function test_mentor_can_access_discussion_resource(): void
    {
        $mentor = User::where('email', 'alex@tradingedu.com')->first();
        $this->assertNotNull($mentor);

        $response = $this->actingAs($mentor)->get('/admin/discussions');
        $response->assertSuccessful();
        $response->assertSee('Discussions');
    }

    public function test_discussion_reply_creation_increments_replies_count(): void
    {
        $discussion = Discussion::first();
        $initialReplies = $discussion->replies_count;
        $user = User::first();

        $reply = DiscussionReply::create([
            'discussion_id' => $discussion->id,
            'user_id' => $user->id,
            'content' => 'Test komentar balasan baru untuk memverifikasi counter.',
            'is_solution' => false,
        ]);

        $this->assertEquals($initialReplies + 1, $discussion->fresh()->replies_count);

        $reply->delete();
        $this->assertEquals($initialReplies, $discussion->fresh()->replies_count);
    }

    public function test_discussion_moderation_approval(): void
    {
        $flagged = Discussion::where('moderation_status', ModerationStatus::Flagged->value)->first();
        $this->assertNotNull($flagged);

        $flagged->update([
            'moderation_status' => ModerationStatus::Approved,
            'status' => DiscussionStatus::Published,
            'reports_count' => 0,
        ]);

        $fresh = $flagged->fresh();
        $this->assertEquals(ModerationStatus::Approved, $fresh->moderation_status);
        $this->assertEquals(DiscussionStatus::Published, $fresh->status);
        $this->assertEquals(0, $fresh->reports_count);
    }

    public function test_discussion_soft_delete(): void
    {
        $discussion = Discussion::first();
        $id = $discussion->id;

        $discussion->delete();
        $this->assertSoftDeleted('discussions', ['id' => $id]);
    }
}
