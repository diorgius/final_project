<?php

namespace Tests\Feature\Webmaster;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use App\Models\User;
use App\Models\Offer;
use App\Models\OfferSubscription;
use App\Events\OfferSubscribeChanged;

class OfferSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_webmaster_can_subscribe_to_offer(): void
    {
        Event::fake();

        $webmaster = User::factory()->webmaster()->create();

        $offer = Offer::factory()->create(['status' => 1,]);

        $response = $this->actingAs($webmaster)
            ->postJson(route('offers.subscribe', $offer));

        $response
            ->assertOk()
            ->assertJsonStructure([
                'success',
                'ref_code',
            ]);

        Event::assertDispatched(OfferSubscribeChanged::class);

        $this->assertDatabaseHas('offer_subscriptions', [
            'offer_id' => $offer->id,
            'webmaster_id' => $webmaster->id,
            'deleted_at' => null,
        ]);
    }

    public function test_webmaster_can_not_subscribe_to_deactive_offer(): void
    {
        $webmaster = User::factory()->webmaster()->create();

        $offer = Offer::factory()->create(['status' => 1,]);

        $offer->delete();

        $response = $this->actingAs($webmaster)
            ->postJson(route('offers.subscribe', $offer));

        $response->assertNotFound();

        $this->assertDatabaseMissing('offer_subscriptions', [
            'offer_id' => $offer->id,
            'webmaster_id' => $webmaster->id,
        ]);
    }

    public function test_webmaster_can_not_subscribe_to_deleted_offer(): void
    {
        $webmaster = User::factory()->webmaster()->create();

        $offer = Offer::factory()->create();

        $response = $this->actingAs($webmaster)
            ->postJson(route('offers.subscribe', $offer));

        $response->assertNotFound();

        $this->assertDatabaseMissing('offer_subscriptions', [
            'offer_id' => $offer->id,
            'webmaster_id' => $webmaster->id,
        ]);
    }

    public function test_subscription_is_not_created_twice(): void
    {
        $webmaster = User::factory()->webmaster()->create();

        $offer = Offer::factory()->create(['status' => 1,]);

        OfferSubscription::factory()->create([
            'offer_id' => $offer->id,
            'webmaster_id' => $webmaster->id,
        ]);

        $this->actingAs($webmaster)
            ->postJson(route('offers.subscribe', $offer));

        $this->assertEquals(
            1,
            OfferSubscription::withTrashed()
                ->where('offer_id', $offer->id)
                ->where('webmaster_id', $webmaster->id)
                ->count()
        );
    }

    public function test_deleted_subscription_is_restored(): void
    {
        $webmaster = User::factory()->webmaster()->create();

        $offer = Offer::factory()->create(['status' => 1,]);

        $subscription = OfferSubscription::factory()->create([
            'offer_id' => $offer->id,
            'webmaster_id' => $webmaster->id,
        ]);

        $subscription->delete();

        $this->assertSoftDeleted($subscription);

        $this->actingAs($webmaster)
            ->postJson(route('offers.subscribe', $offer));

        $this->assertDatabaseHas('offer_subscriptions', [
            'id' => $subscription->id,
            'deleted_at' => null,
        ]);
    }

    public function test_webmaster_can_unsubscribe(): void
    {
        Event::fake();

        $webmaster = User::factory()->webmaster()->create();

        $offer = Offer::factory()->create(['status' => 1,]);

        $subscription = OfferSubscription::factory()->create([
            'offer_id' => $offer->id,
            'webmaster_id' => $webmaster->id,
        ]);

        $response = $this->actingAs($webmaster)
            ->postJson(route('offers.unsubscribe', $offer));

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        Event::assertDispatched(OfferSubscribeChanged::class);            

        $this->assertSoftDeleted($subscription);
    }

    public function test_admin_can_not_subscribe(): void
    {
        $advertiser = User::factory()->admin()->create();

        $offer = Offer::factory()->create(['status' => 1,]);

        $this->actingAs($advertiser)
            ->postJson(route('offers.subscribe', $offer))
            ->assertForbidden();
    }

    public function test_advertiser_can_not_subscribe(): void
    {
        $advertiser = User::factory()->advertiser()->create();

        $offer = Offer::factory()->create(['status' => 1,]);

        $this->actingAs($advertiser)
            ->postJson(route('offers.subscribe', $offer))
            ->assertForbidden();
    }
}
