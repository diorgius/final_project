<?php

namespace Tests\Feature\Advertiser;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\OfferTheme;

class OfferThemeTest extends TestCase
{
    use RefreshDatabase;

    public function test_advertiser_can_create_offer_theme(): void
    {
        $advertiser = User::factory()->advertiser()->create();

        $response = $this->actingAs($advertiser)
            ->post(route('themes.store'), [
                'name' => 'Finance',
            ]);

        $response->assertRedirect(route('themes.index'));

        $this->assertDatabaseHas('offer_themes', [
            'name' => 'Finance',
        ]);
    }

    public function test_theme_name_must_be_unique(): void
    {
        $advertiser = User::factory()->advertiser()->create();

        OfferTheme::factory()->create([
            'name' => 'Finance',
        ]);

        $response = $this->actingAs($advertiser)
            ->post(route('themes.store'), [
                'name' => 'Finance',
            ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_theme_name_is_required(): void
    {
        $advertiser = User::factory()->advertiser()->create();

        $response = $this->actingAs($advertiser)
            ->post(route('themes.store'), []);

        $response->assertSessionHasErrors('name');
        
    }
}