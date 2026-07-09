<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class CheckActiveTest extends TestCase
{
    use RefreshDatabase;

    public function test_blocked_user_is_logged_out(): void
    {
        $user = User::factory()->admin()->create([
            'status' => 0,
        ]);

        $response = $this->actingAs($user)
            ->get(route('admin.main'));

        $this->assertGuest();

        $response->assertRedirect(route('login'));
        
        $response->assertSessionHasErrors(['email' => __('http-statuses.423'),]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('admin.main'));

        $response->assertRedirect(route('login'));
    }
}