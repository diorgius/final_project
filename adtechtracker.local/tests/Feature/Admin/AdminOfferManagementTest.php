<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Offer;

class AdminOfferManagementTest extends TestCase
{
    use RefreshDatabase;

    // тест удаления оффера админом
    public function test_admin_can_delete_offer(): void
    {
        // создаем админа
        $admin = User::factory()->admin()->create();

        // создаем рекламщика
        $advertiser = User::factory()->advertiser()->create();

        // создаем оффер
        $offer = Offer::factory()->create([
            'advertiser_id' => $advertiser->id,
        ]);

        // удаляем оффер админом
        $response = $this->actingAs($admin)
            ->delete(route('admin.offers.destroy', $offer->id));

        // редирект
        $response->assertRedirect(route('admin.offers'));

        // проверяем магкое удаление
        $this->assertSoftDeleted($offer);
    }

    // тест изменения статуса оффера админом
    public function test_admin_can_change_offer_status(): void
    {
        $admin = User::factory()->admin()->create();

        $advertiser = User::factory()->advertiser()->create();

        $offer = Offer::factory()->create([
            'advertiser_id' => $advertiser->id,
        ]);

        $response = $this->actingAs($admin)
            ->postJson(route('admin.offers.status', $offer), [
                'status' => 1,
            ]);

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('offers', [
            'id' => $offer->id,
            'status' => 1,
        ]);
    }
}
