<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    // тест админ создет рекламщика
    public function test_admin_can_create_advertiser(): void
    {
        // создаем админа
        $admin = User::factory()->admin()->create();

        // админ создает рекламщика
        $response = $this->actingAs($admin)->post(route('users.store'), [
            'name' => 'Test Advertiser',
            'email' => 'advertiser@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'advertiser',
            'locale' => 'ru',
            'status' => 1,
        ]);

        // редирект
        $response->assertRedirect(route('users.index'));

        // проверяем создание в БД
        $this->assertDatabaseHas('users', [
            'email' => 'advertiser@test.com',
            'role' => 'advertiser',
        ]);
    }

    // тест уникальности email
    public function test_email_must_be_unique(): void
    {
        $admin = User::factory()->admin()->create();

        // создаем рекламщика с email
        User::factory()->advertiser()->create([
            'email' => 'advertiser@test.com',
        ]);

        // админ создает рекламщика с таким же email
        $response = $this->actingAs($admin)->post(route('users.store'), [
            'name' => 'Test',
            'email' => 'advertiser@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'advertiser',
            'locale' => 'ru',
            'status' => 1,
        ]);

        // проверяем вывод ошибки
        $response->assertSessionHasErrors('email');
    }

    // тест админ может редактировать пользователя
    public function test_admin_can_edit_user(): void
    {
        $admin = User::factory()->admin()->create();

        $user = User::factory()->advertiser()->create();

        $response = $this->actingAs($admin)
            ->put(route('users.update', $user), [
                'name' => 'New Name',
                'email' => $user->email,
                'password' => 'password',
                'role' => 'advertiser',
                // 'status' => 1, // проверка изменения статуса
            ]);

        $response->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
            // 'status' => 1, // проверка изменения статуса
            'status' => 0,
        ]);
    }

    public function test_admin_can_delete_user(): void
    {
        $admin = User::factory()->admin()->create();

        $user = User::factory()->advertiser()->create();

        $response = $this->actingAs($admin)
            ->delete(route('users.destroy', $user));

        $response->assertRedirect(route('users.index'));

        $this->assertSoftDeleted($user);
    }

    public function test_admin_can_restore_deleted_user(): void
    {
        $admin = User::factory()->admin()->create();

        $user = User::factory()->advertiser()->create();

        $user->delete();

        $response = $this->actingAs($admin)
            ->patch(route('users.restore', $user->id));

        $response->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'deleted_at' => null,
        ]);
    }
}
