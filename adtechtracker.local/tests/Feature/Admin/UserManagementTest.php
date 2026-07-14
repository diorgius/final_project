<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use App\Models\User;
use App\Models\Offer;
use App\Models\OfferSubscription;
use App\Events\OfferCreate;
use App\Events\OfferSubscribeChanged;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_admin_can_edit_user(): void
    {
        $admin = User::factory()->admin()->create();

        $user = User::factory()->advertiser()->create();

        $response = $this->actingAs($admin)
            ->patch(route('users.update', $user), [
                'name' => 'New Name',
                'email' => $user->email,
                'password' => 'password',
                'role' => 'webmaster',
                'status' => 1,
            ]);

        $response->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
            'role' => 'webmaster',
            'status' => 1,
        ]);
    }

    public function test_admin_can_change_status_user(): void
    {
        $admin = User::factory()->admin()->create();

        $user = User::factory()->advertiser()->create([
            'status' => 0,
        ]);

        $response = $this->actingAs($admin)
            ->patch(route('users.update', $user), [
                'name' => $user->name,
                'email' => $user->email,
                'password' => $user->password,
                'role' => $user->role,
                'status' => 1,
            ]);

        $response->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'status' => 1,
        ]);
    }
    public function test_another_user_can_not_edit_user(): void
    {
        $advertiser = User::factory()->advertiser()->create();

        $user = User::factory()->advertiser()->create();

        $response = $this->actingAs($advertiser)
            ->get(route('users.edit', $user));

        $response->assertForbidden();
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

    public function test_another_user_can_not_delete_user(): void
    {
        $advertiser = User::factory()->advertiser()->create();

        $user = User::factory()->advertiser()->create();

        $response = $this->actingAs($advertiser)
            ->delete(route('users.destroy', $user));

        $response->assertForbidden();
    }

    public function test_admin_can_not_delete_himself(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)
            ->delete(route('users.destroy', $admin));

        $response->assertSessionHasErrors('email');
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

    public function test_admin_can_restore_deleted_advertiser_with_offer_with_subsriptions(): void
    {
        // создаем событие
        Event::fake();

        $admin = User::factory()->admin()->create();

        $advertiser = User::factory()->advertiser()->create();

        $webmaster = User::factory()->webmaster()->create();

        // создаем оффер
        $offer = Offer::factory()->create([
            'advertiser_id' => $advertiser->id,
        ]);

        // создаем подписку
        $subscription = OfferSubscription::factory()->create([
            'offer_id' => $offer->id,
            'webmaster_id' => $webmaster->id,
        ]);

        // удаляем пользователя
        $advertiser->delete();
        
        // удаляем оффер
        $offer->delete();

        // удаляем подписку
        $subscription->delete();

        // проверяем удаление
        $this->assertSoftDeleted($advertiser);

        $this->assertSoftDeleted($offer);

        $this->assertSoftDeleted($subscription);

        // восстанавливаем прользователя
        $response = $this->actingAs($admin)
            ->patch(route('users.restore', $advertiser->id));

        // слушаем событие
        Event::assertDispatched(OfferCreate::class);

        Event::assertDispatched(OfferSubscribeChanged::class);

        // редирект
        $response->assertRedirect(route('users.index'));

        // проверяем в БД
        $this->assertDatabaseHas('users', [
            'id' => $advertiser->id,
            'deleted_at' => null,
        ]);

        $this->assertDatabaseHas('offers', [
            'id' => $offer->id,
            'deleted_at' => null,
        ]);

        $this->assertDatabaseHas('offer_subscriptions', [
            'id' => $subscription->id,
            'deleted_at' => null,
        ]);
    }

    public function test_admin_can_restore_deleted_webmaster_with_subsriptions(): void
    {
        Event::fake();

        $admin = User::factory()->admin()->create();

        $advertiser = User::factory()->advertiser()->create();

        $webmaster = User::factory()->webmaster()->create();

        $offer = Offer::factory()->create([
            'advertiser_id' => $advertiser->id,
        ]);

        $subscription = OfferSubscription::factory()->create([
            'offer_id' => $offer->id,
            'webmaster_id' => $webmaster->id,
        ]);

        $webmaster->delete();
        
        $subscription->delete();

        $this->assertSoftDeleted($webmaster);

        $this->assertSoftDeleted($subscription);

        $response = $this->actingAs($admin)
            ->patch(route('users.restore', $webmaster->id));

        Event::assertDispatched(OfferSubscribeChanged::class);

        $response->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'id' => $webmaster->id,
            'deleted_at' => null,
        ]);

        $this->assertDatabaseHas('offer_subscriptions', [
            'id' => $subscription->id,
            'deleted_at' => null,
        ]);
    }
}
