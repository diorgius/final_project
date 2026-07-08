<?php

namespace Tests\Feature\Advertiser;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use App\Models\User;
use App\Models\Offer;
use App\Models\OfferSubscription;
use App\Events\OfferCreate;
use App\Events\OfferDelete;
use App\Events\OfferStatusChanged;


class OfferManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_advertiser_can_create_offer(): void
    {
        Event::fake();

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

        Event::assertDispatched(OfferCreate::class);

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

    public function test_advertiser_can_not_edit_forieng_offer(): void 
    {
        $owner = User::factory()->advertiser()->create();

        $other = User::factory()->advertiser()->create();

        $offer = Offer::factory()->create([
            'advertiser_id' => $owner->id,
        ]);

        $response = $this->actingAs($other)
            ->get(route('offers.edit', $offer->id));

        $response->assertForbidden();
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
        Event::fake();

        $advertiser = User::factory()->advertiser()->create();

        $offer = Offer::factory()->create([
            'advertiser_id' => $advertiser->id,
        ]);

        $response = $this->actingAs($advertiser)
            ->delete(route('advertiser.offers.destroy', $offer->id));

        $response->assertRedirect(route('advertiser.offers'));

        Event::assertDispatched(OfferDelete::class);

        $this->assertSoftDeleted($offer);
    }

    public function test_advertiser_can_not_delete_foreign_offer(): void
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

    public function test_advertiser_can_restore_offer(): void
    {
        Event::fake();

        $advertiser = User::factory()->advertiser()->create();

        $offer = Offer::factory()->create([
            'advertiser_id' => $advertiser->id,
        ]);

        $offer->delete();

        $response = $this->actingAs($advertiser)
            ->patch(route('offers.restore', $offer->id));

        $response->assertRedirect(route('advertiser.offers'));

        Event::assertDispatched(OfferCreate::class);

        $this->assertDatabaseHas('offers', [
            'id' => $offer->id,
            'deleted_at' => null,
        ]);
    }

    public function test_advertiser_can_restore_offer_with_subscriptions(): void
    {
        Event::fake();

        $advertiser = User::factory()->advertiser()->create();

        $webmaster = User::factory()->webmaster()->create();

        $offer = Offer::factory()->create([
            'advertiser_id' => $advertiser->id,
        ]);

        $subscription = OfferSubscription::factory()->create([
            'offer_id' => $offer->id,
            'webmaster_id' => $webmaster->id,
        ]);

        $offer->delete();

        $subscription->delete();

        $this->assertSoftDeleted($offer);

        $this->assertSoftDeleted($subscription);

        $this->actingAs($advertiser)
            ->patch(route('offers.restore', $offer->id));

        Event::assertDispatched(OfferCreate::class);

        $this->assertDatabaseHas('offers', [
            'id' => $offer->id,
            'deleted_at' => null,
        ]);

        $this->assertDatabaseHas('offer_subscriptions', [
            'id' => $subscription->id,
            'deleted_at' => null,
        ]);
    }

    public function test_advertiser_can_not_restore_foreign_offer(): void
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

    public function test_deleted_offer_is_found_and_restore_offer_is_added_to_session(): void
{
    $advertiser = User::factory()->advertiser()->create();

    $offer = Offer::factory()->create([
        'advertiser_id' => $advertiser->id,
        'url' => 'https://offer.test',
    ]);

    $offer->delete();

    $response = $this->actingAs($advertiser)
        ->from(route('advertiser.offers'))
        ->post(route('offers.store'), [
            'name' => 'New offer',
            'url' => 'https://offer.test',
            'price' => 100,
            'theme' => $offer->theme_id,
        ]);

    $response->assertRedirect(route('advertiser.offers'));

    $response->assertSessionHas('restore_offer', function ($data) use ($offer) {
        return $data['id'] === $offer->id
            && $data['url'] === $offer->url
            && $data['theme'] === $offer->theme->name;
        });
    }

    public function test_advertiser_can_change_offer_status(): void
    {
        Event::fake();

        $advertiser = User::factory()->advertiser()->create();

        $offer = Offer::factory()->create([
            'advertiser_id' => $advertiser->id,
        ]);

        $response = $this->actingAs($advertiser)
            ->postJson(route('advertiser.offers.status', $offer), [
                'status' => 1,
            ]);

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        Event::assertDispatched(OfferStatusChanged::class);

        $this->assertDatabaseHas('offers', [
            'id' => $offer->id,
            'status' => 1,
        ]);
    }

    public function test_advertiser_can_not_change_foreign_offer_status(): void
    {
        $owner = User::factory()->advertiser()->create();

        $other = User::factory()->advertiser()->create();

        $offer = Offer::factory()->create([
            'advertiser_id' => $owner->id,
        ]);

        $response = $this->actingAs($other)
            ->postJson(route('advertiser.offers.status', $offer), [
                'status' => 1,
            ]);

        $response->assertForbidden();
    }
}
