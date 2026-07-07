<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class CheckAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_admin_routes(): void
    {
        $user = User::factory()->admin()->create();

        $response = $this->actingAs($user)
            ->get('/test-admin');

        $response->assertOk();
    }

    public function test_advertiser_can_not_access_admin_routes(): void
    {
        $user = User::factory()->advertiser()->create();

        $response = $this->actingAs($user)
            ->get('/test-admin');

        $response->assertForbidden();
    }

    public function test_webmaster_can_not_access_admin_routes(): void
    {
        $user = User::factory()->webmaster()->create();

        $response = $this->actingAs($user)
            ->get('/test-admin');

        $response->assertForbidden();
    }
}