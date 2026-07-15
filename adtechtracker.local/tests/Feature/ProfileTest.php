<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->admin()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_user_can_change_locale(): void
    {
        $user = User::factory()->admin()->create([
            'locale' => 'ru',
        ]);

        $response = $this->actingAs($user)
            ->patch(route('profile.language.update'), [
                'lang' => 'en',
            ]);

        $response
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHas('status', 'locale-updated');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'locale' => 'en',
        ]);

        $response->assertSessionHas('locale', 'en');
    }

    public function test_invalid_locale_is_rejected(): void
    {
        $user = User::factory()->admin()->create([
            'locale' => 'ru',
        ]);

        $response = $this->actingAs($user)
            ->patch(route('profile.language.update'), [
                'lang' => 'fr',
            ]);

        $response->assertSessionHasErrors('lang');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'locale' => 'ru',
        ]);
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->admin()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->admin()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->advertiser()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertSoftDeleted($user);
    }


    public function test_last_admin_can_not_delete_himself(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this
            ->actingAs($admin)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response->assertSessionHasErrors();

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
        ]);
    }

    public function test_admin_can_delete_himself_if_admin_more_than_one(): void
    {
        $admin = User::factory()->admin()->create();

        User::factory()->admin()->create();

        $response = $this
            ->actingAs($admin)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response->assertSessionHasNoErrors();

        $this->assertSoftDeleted('users', [
            'id' => $admin->id,
        ]);
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->admin()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }
}
