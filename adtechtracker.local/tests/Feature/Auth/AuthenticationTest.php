<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_admin_can_login(): void
    {
        $user = User::factory()->admin()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('admin.main', absolute: false));
    }
    
    public function test_advertiser_can_login(): void
    {
        $user = User::factory()->advertiser()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('advertiser.offers', absolute: false));
    }

    public function test_webmaster_can_login(): void
    {
        $user = User::factory()->webmaster()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('webmaster.offers', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->admin()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors(['email' => __('auth.failed'),]);
    }

    public function test_users_can_not_authenticate_with_unknown_email(): void
    {
        $response = $this->post('/login', [
            'email' => 'unknown@unknown.unknown',
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors(['email' => __('auth.failed'),]);
    }

    public function test_blocked_user_can_not_login(): void
    {
        $user = User::factory()->admin()->create([
            'status' => 0,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors(['email' => __('http-statuses.423'),]);
    }

    public function test_login_is_rate_limited(): void
    {
        foreach (range(1, 6) as $i) {

            $this->post('/login', [
                'email' => 'unknown@unknown.unknown',
                'password' => 'wrong-password',
            ]);
        }

        $response = $this->post('/login', [
            'email' => 'unknown@unknown.unknown',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->admin()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
