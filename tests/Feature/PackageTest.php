<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PackageTest extends TestCase
{
    public function test_can_list_course_packages(): void
    {
        $response = $this->getJson('/api/v1/packages');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'price',
                        'features',
                        'courses',
                        'mentors',
                        'stats',
                    ],
                ],
            ]);
    }

    public function test_can_view_course_package_detail(): void
    {
        $response = $this->getJson('/api/v1/packages/smc-mastery-package');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'slug' => 'smc-mastery-package',
                ],
            ]);
    }

    public function test_returns_404_for_invalid_package(): void
    {
        $response = $this->getJson('/api/v1/packages/invalid-package-slug');

        $response->assertStatus(404);
    }

    public function test_can_checkout_course_package(): void
    {
        $response = $this->postJson('/api/v1/packages/starter-trader-bundle/checkout', [
            'payment_method' => 'BCA Virtual Account',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }
}
