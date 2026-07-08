<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Commission;

class CommissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_commission(): void
    {
        $admin = User::factory()->admin()->create();

        $commission = Commission::create([
            'commission' => 20,
        ]);

        $response = $this->actingAs($admin)
            ->patch(route('commission.update', $commission), [
                'commission' => 15,
            ]);

        $response->assertRedirect(route('admin.main'));

        $this->assertDatabaseHas('commissions', [
            'id' => $commission->id,
            'commission' => 15,
        ]);
    }

    public function test_commission_can_not_null(): void
    {
        $admin = User::factory()->admin()->create();

        $commission = Commission::create();

        $response = $this->actingAs($admin)
            ->patch(route('commission.update', $commission), []);

        $response->assertSessionHasErrors('commission');
    }
    
    public function test_commission_must_be_numeric(): void
    {
        $admin = User::factory()->admin()->create();

        $commission = Commission::create();

        $response = $this->actingAs($admin)
            ->patch(route('commission.update', $commission), [
                'commission' => 'abc',
            ]);

        $response->assertSessionHasErrors('commission');
    }

    public function test_commission_can_not_be_less_0(): void
    {
        $admin = User::factory()->admin()->create();

        $commission = Commission::create();

        $response = $this->actingAs($admin)
            ->patch(route('commission.update', $commission), [
                'commission' => -1,
            ]);

        $response->assertSessionHasErrors('commission');
    }

    public function test_commission_can_not_be_more_100(): void
    {
        $admin = User::factory()->admin()->create();

        $commission = Commission::create();

        $response = $this->actingAs($admin)
            ->patch(route('commission.update', $commission), [
                'commission' => 101,
            ]);

        $response->assertSessionHasErrors('commission');
    }
}