<?php

namespace Tests\Feature;

use App\Domain\Identity\Models\MentorProfile;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserManagementResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_admin_can_access_users_resource(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->get('/admin/users');
        $response->assertSuccessful();
        $response->assertSee('Super Admin TradingEdu');
    }

    public function test_admin_can_access_mentors_resource(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->get('/admin/mentors');
        $response->assertSuccessful();
        $response->assertSee('Alex Wijaya');
        $response->assertSee('Sarah Tan');
    }

    public function test_admin_can_access_mentor_create_page(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $response = $this->actingAs($admin)->get('/admin/mentors/create');
        $response->assertSuccessful();
    }

    public function test_admin_can_access_roles_permissions_resource(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->get('/admin/roles-permissions');
        $response->assertSuccessful();
        $response->assertSee('SUPER ADMIN');
        $response->assertSee('MENTOR');
    }

    public function test_admin_can_access_role_create_page(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $response = $this->actingAs($admin)->get('/admin/roles-permissions/create');
        $response->assertSuccessful();
    }

    public function test_unauthenticated_user_redirected_to_login(): void
    {
        $response = $this->get('/admin/mentors');
        $response->assertRedirect('/admin/login');
    }

    public function test_customer_cannot_access_backoffice(): void
    {
        $customer = User::where('email', 'customer@tradingedu.com')->first();
        $this->assertNotNull($customer);

        $response = $this->actingAs($customer)->get('/admin');
        $response->assertForbidden();
    }
}
