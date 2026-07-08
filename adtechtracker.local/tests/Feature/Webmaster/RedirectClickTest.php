<?php

namespace Tests\Feature\Webmaster;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Offer;
use App\Models\OfferSubscription;
use App\Models\Commission;

class RedirectClickTest extends TestCase
{
    use RefreshDatabase;

    public function test_invalid_ref_code_returns_404(): void
    {
        $response = $this->get('/r/invalid-ref-code');

        $response->assertNotFound();

        $this->assertDatabaseHas('offer_access_logs', [
            'ref_code' => 'invalid-ref-code',
            'status' => 'rejected',
            'reason' => 'invalid_ref',
        ]);

        $this->assertDatabaseCount('offer_clicks', 0);
    }

    public function test_deleted_subscription_returns_404(): void
    {
        $offer = Offer::factory()->create(['status' => 1],);

        $webmaster = User::factory()->webmaster()->create();

        $subscription = OfferSubscription::factory()->create([
            'offer_id' => $offer->id,
            'webmaster_id' => $webmaster->id,
        ]);

        $subscription->delete();

        $response = $this->get('/r/' . $subscription->ref_code);

        $response->assertNotFound();

        $this->assertDatabaseHas('offer_access_logs', [
            'subscription_id' => $subscription->id,
            'status' => 'rejected',
            'reason' => 'subscription_inactive',
        ]);

        $this->assertDatabaseCount('offer_clicks', 0);
    }

    public function test_inactive_offer_returns_404(): void
    {
        $offer = Offer::factory()->create(['status' => 0], );

        $webmaster = User::factory()->webmaster()->create();

        $subscription = OfferSubscription::factory()->create([
            'offer_id' => $offer->id,
            'webmaster_id' => $webmaster->id,
        ]);

        $response = $this->get('/r/' . $subscription->ref_code);

        $response->assertNotFound();

        $this->assertDatabaseHas('offer_access_logs', [
            'subscription_id' => $subscription->id,
            'status' => 'rejected',
            'reason' => 'inactive_offer',
        ]);

        $this->assertDatabaseCount('offer_clicks', 0);
    }

    public function test_offer_redirect_creates_click(): void
    {
        Commission::create([
            'commission' => 20,
        ]);

        $advertiser = User::factory()->advertiser()->create();

        $webmaster = User::factory()->webmaster()->create();

        $offer = Offer::factory()->create([
            'advertiser_id' => $advertiser->id,
            'price' => 100,
            'url' => 'https://example.ru',
            'status' => 1,
        ]);

        $subscription = OfferSubscription::factory()->create([
            'offer_id' => $offer->id,
            'webmaster_id' => $webmaster->id,
        ]);

        $response = $this->get('/r/' . $subscription->ref_code);

        $response->assertRedirect('https://example.ru');

        $this->assertDatabaseHas('offer_access_logs', [
            'subscription_id' => $subscription->id,
            'status' => 'allowed',
        ]);

        $this->assertDatabaseHas('offer_clicks', [
            'subscription_id' => $subscription->id,
            'advertiser_cost' => 100,
            'webmaster_income' => 80,
            'system_commission' => 20,
        ]);
    }

    public function test_commission_is_calculated_correctly(): void
    {
        Commission::create([
            'commission' => 17,
        ]);

        $advertiser = User::factory()->advertiser()->create();

        $webmaster = User::factory()->webmaster()->create();

        $offer = Offer::factory()->create([
            'advertiser_id' => $advertiser->id,
            'price' => 150,
            'url' => 'https://example.ru',
            'status' => 1,
        ]);

        $subscription = OfferSubscription::factory()->create([
            'offer_id' => $offer->id,
            'webmaster_id' => $webmaster->id,
        ]);

        $this->get('/r/' . $subscription->ref_code);

        $this->assertDatabaseHas('offer_clicks', [
            'advertiser_cost' => 150,
            'webmaster_income' => 124.5,
            'system_commission' => 25.5,
        ]);
    }
}
