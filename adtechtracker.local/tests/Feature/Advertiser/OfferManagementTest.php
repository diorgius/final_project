<?php

namespace Tests\Feature\Advertiser;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Offer;
use App\Models\OfferTheme;

class OfferManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_advertiser_can_create_offer(): void
    {
        $advertiser = User::factory()->advertiser()->create();

        $offer = Offer::factory()->make([
            'advertiser_id' => $advertiser->id,
        ]);

        $response = $this->actingAs($advertiser)
            ->post(route('offers.store'), [
                'name' => $offer->name,
                'url' => $offer->url,
                'price' => $offer->price,
                'theme' => $offer->theme_id,
            ]);

        $response->assertRedirect(route('advertiser.offers'));

        $this->assertDatabaseHas('offers', [
            'name' => $offer->name,
            'url' => $offer->url,
            'advertiser_id' => $advertiser->id,
        ]);
    }

    public function test_offer_url_must_be_unique_on_create(): void
    {
        $advertiser = User::factory()->advertiser()->create();

        Offer::factory()->create([
            'advertiser_id' => $advertiser->id,
            'url' => 'https://offer1.test',
        ]);

        $offer = Offer::factory()->create([
            'advertiser_id' => $advertiser->id,
            'url' => 'https://offer2.test',
        ]);

        $response = $this->actingAs($advertiser)
            ->post(route('offers.store', $offer->id), [
                'name' => $offer->name,
                'url' => 'https://offer1.test',
                'price' => $offer->price,
                'theme' => $offer->theme_id,
            ]);

        $response->assertSessionHasErrors(['url' => __('offers.offer_exists'),]);
    }

    public function test_advertiser_can_edit_offer(): void
    {
        $advertiser = User::factory()->advertiser()->create();

        $offer = Offer::factory()->create([
            'advertiser_id' => $advertiser->id,
        ]);

        $response = $this->actingAs($advertiser)
            ->patch(route('offers.update', $offer->id), [
                'name' => 'New offer name',
                'url' => 'https://new-url.test',
                'price' => 150,
                'theme' => $offer->theme_id,
            ]);

        $response->assertRedirect(route('advertiser.offers'));

        $this->assertDatabaseHas('offers', [
            'id' => $offer->id,
            'name' => 'New offer name',
            'url' => 'https://new-url.test',
            'price' => 150,
        ]);
    }

    public function test_offer_url_must_be_unique_on_update(): void
    {
        $advertiser = User::factory()->advertiser()->create();

        Offer::factory()->create([
            'advertiser_id' => $advertiser->id,
            'url' => 'https://offer1.test',
        ]);

        $offer = Offer::factory()->create([
            'advertiser_id' => $advertiser->id,
            'url' => 'https://offer2.test',
        ]);

        $response = $this->actingAs($advertiser)
            ->patch(route('offers.update', $offer->id), [
                'name' => $offer->name,
                'url' => 'https://offer1.test',
                'price' => $offer->price,
                'theme' => $offer->theme_id,
            ]);

        $response->assertSessionHasErrors(['url' => __('offers.offer_exists'),]);
    }

    public function test_advertiser_can_delete_offer(): void
    {
        $advertiser = User::factory()->advertiser()->create();

        $offer = Offer::factory()->create([
            'advertiser_id' => $advertiser->id,
        ]);

        $response = $this->actingAs($advertiser)
            ->delete(route('advertiser.offers.destroy', $offer->id));

        $response->assertRedirect(route('advertiser.offers'));

        $this->assertSoftDeleted($offer);
    }

    public function test_advertiser_can_restore_offer(): void
    {
        $advertiser = User::factory()->advertiser()->create();

        $offer = Offer::factory()->create([
            'advertiser_id' => $advertiser->id,
        ]);

        $offer->delete();

        $response = $this->actingAs($advertiser)
            ->patch(route('offers.restore', $offer->id));

        $response->assertRedirect(route('advertiser.offers'));

        $this->assertDatabaseHas('offers', [
            'id' => $offer->id,
            'deleted_at' => null,
        ]);
    }

    public function test_advertiser_cannot_delete_foreign_offer(): void
    {
        $owner = User::factory()->advertiser()->create();

        $other = User::factory()->advertiser()->create();

        $offer = Offer::factory()->create([
            'advertiser_id' => $owner->id,
        ]);

        $response = $this->actingAs($other)
            ->delete(route('advertiser.offers.destroy', $offer->id));

        $response->assertForbidden();

        $this->assertDatabaseHas('offers', [
            'id' => $offer->id,
            'deleted_at' => null,
        ]);
    }

    public function test_advertiser_cannot_restore_foreign_offer(): void
    {
        $owner = User::factory()->advertiser()->create();

        $other = User::factory()->advertiser()->create();

        $offer = Offer::factory()->create([
            'advertiser_id' => $owner->id,
        ]);

        $offer->delete();

        $response = $this->actingAs($other)
            ->patch(route('offers.restore', $offer->id));

        $response->assertForbidden();

        $this->assertSoftDeleted($offer);
    }
}
