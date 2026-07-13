<?php

namespace Tests\Feature\Middleware;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class CheckWebmasterTest extends TestCase
{
    use RefreshDatabase;

    public function test_webmaster_can_access_webmaster_routes(): void
    {
        $user = User::factory()->webmaster()->create();

        $response = $this->actingAs($user)
            ->get('/test-webmaster');

        $response->assertOk();
    }

    public function test_admin_can_not_access_webmaster_routes(): void
    {
        $user = User::factory()->admin()->create();

        $response = $this->actingAs($user)
            ->get('/test-webmaster');

        $response->assertForbidden();
    }

    public function test_advertiser_can_not_access_webmaster_routes(): void
    {
        $user = User::factory()->advertiser()->create();

        $response = $this->actingAs($user)
            ->get('/test-webmaster');

        $response->assertForbidden();
    }
}