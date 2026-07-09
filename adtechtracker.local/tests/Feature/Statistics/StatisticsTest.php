<?php

namespace Tests\Feature\Statistics;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Offer;
use App\Models\OfferSubscription;
use App\Models\OfferClick;
use Carbon\Carbon;

class StatisticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_statistics(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)
            ->get(route('admin.statistics'));

        $response
            ->assertOk()
            ->assertViewIs('admin.statistics')
            ->assertViewHas([
                'advertisers',
                'webmasters',
                'offers',
                'activeOffers',
                'deactiveOffers',
                'deletedOffers',
                'subscriptions',
                'activeSubscriptions',
                'deactiveSubscriptions',
                'clicks',
                'rejectedClicks',
                'advertiserExpenses',
                'webmasterIncome',
                'systemProfit',
            ]);
    }

    public function test_advertiser_can_open_statistics(): void
    {
        $advertiser = User::factory()->advertiser()->create();

        Offer::factory()->create([
            'advertiser_id' => $advertiser->id,
        ]);

        $response = $this->actingAs($advertiser)
            ->get(route('advertiser.statistics'));

        $response
            ->assertOk()
            ->assertViewIs('advertiser.statistics')
            ->assertViewHas([
                'offers',
                'totalClicks',
                'totalExpenses',
            ]);
    }

    public function test_webmster_can_open_statistics(): void
    {
        $webmaster = User::factory()->webmaster()->create();

        $advertiser = User::factory()->advertiser()->create();

        Offer::factory()->create([
            'advertiser_id' => $advertiser->id,
        ]);

        $response = $this->actingAs($webmaster)
            ->get(route('webmaster.statistics'));

        $response
            ->assertOk()
            ->assertViewIs('webmaster.statistics')
            ->assertViewHas([
                'offers',
                'totalClicks',
                'totalRevenue',
            ]);
    }

    public function test_admin_can_get_statistics_summary(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)
            ->getJson(route('admin.summary', [
                'period' => 'all',
            ]));

        $response
            ->assertOk()
            ->assertJsonStructure([
                'advertisers',
                'webmasters',
                'offers',
                'activeOffers',
                'deactiveOffers',
                'deletedOffers',
                'subscriptions',
                'activeSubscriptions',
                'deactiveSubscriptions',
                'clicks',
                'rejectedClicks',
                'advertiserExpenses',
                'webmasterIncome',
                'systemProfit',
            ]);
    }
    public function test_advertiser_can_get_statistics_summary(): void
    {
        $advertiser = User::factory()->advertiser()->create();

        $response = $this->actingAs($advertiser)
            ->getJson(route('advertiser.summary', [
                'period' => 'all',
            ]));

        $response->assertJsonStructure([
            'offers',
            'totalClicks',
            'totalExpenses',
        ]);
    }

    public function test_webmaster_can_get_statistics_summary(): void
    {
        $webmaster = User::factory()->webmaster()->create();

        $response = $this->actingAs($webmaster)
            ->getJson(route('webmaster.summary', [
                'period' => 'all',
            ]));

        $response->assertJsonStructure([
            'offers',
            'totalClicks',
            'totalRevenue',
        ]);
    }

    public function test_admin_summary_filters_clicks_sum_by_period(): void
    {
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

        // клик за сегодня
        OfferClick::create([
            'offer_id' => $offer->id,
            'advertiser_id' => $advertiser->id,
            'webmaster_id' => $webmaster->id,
            'subscription_id' => $subscription->id,
            'ref_code' => $subscription->ref_code,
            'target_url' => 'http://example.ru',
            'advertiser_cost' => 100,
            'webmaster_income' => 80,
            'system_commission' => 20,
        ]);

        // клик месяц назад
        $click = OfferClick::create([
            'offer_id' => $offer->id,
            'advertiser_id' => $advertiser->id,
            'webmaster_id' => $webmaster->id,
            'subscription_id' => $subscription->id,
            'ref_code' => $subscription->ref_code,
            'target_url' => 'http://example.ru',
            'advertiser_cost' => 100,
            'webmaster_income' => 80,
            'system_commission' => 20,
        ]);

        // изменяем дату у последнего клика
        OfferClick::whereKey($click->id)->update([
            'created_at' => now()->subMonth(),
        ]);

        // получаем данные за день
        $response = $this->actingAs($admin)
            ->getJson(route('admin.summary', [
                'period' => 'day',
            ]));

        // сравниваем ответ
        $response
            ->assertOk()
            ->assertJson([
                'clicks' => 1,
                'advertiserExpenses' => 100.00,
                'webmasterIncome' => 80.00,
                'systemProfit' => 20.00,
            ]);

        // получаем данные за весь период
        $response = $this->actingAs($admin)
            ->getJson(route('admin.summary', [
                'period' => 'all',
            ]));

        // сравниваем ответ, должно быть больше в два раза
        $response
            ->assertOk()
            ->assertJson([
                'clicks' => 2,
                'advertiserExpenses' => 200.00,
                'webmasterIncome' => 160.00,
                'systemProfit' => 40.00,
            ]);
    }

    public function test_advertiser_summary_filters_clicks_sum_by_period(): void
    {

        $advertiser = User::factory()->advertiser()->create();

        $webmaster = User::factory()->webmaster()->create();

        $offer = Offer::factory()->create([
            'advertiser_id' => $advertiser->id,
        ]);

        $subscription = OfferSubscription::factory()->create([
            'offer_id' => $offer->id,
            'webmaster_id' => $webmaster->id,
        ]);

        // клик за сегодня
        OfferClick::create([
            'offer_id' => $offer->id,
            'advertiser_id' => $advertiser->id,
            'webmaster_id' => $webmaster->id,
            'subscription_id' => $subscription->id,
            'ref_code' => $subscription->ref_code,
            'target_url' => 'http://example.ru',
            'advertiser_cost' => 100,
            'webmaster_income' => 80,
            'system_commission' => 20,
        ]);

        // клик месяц назад
        $click = OfferClick::create([
            'offer_id' => $offer->id,
            'advertiser_id' => $advertiser->id,
            'webmaster_id' => $webmaster->id,
            'subscription_id' => $subscription->id,
            'ref_code' => $subscription->ref_code,
            'target_url' => 'http://example.ru',
            'advertiser_cost' => 100,
            'webmaster_income' => 80,
            'system_commission' => 20,
        ]);

        // изменяем дату у последнего клика
        OfferClick::whereKey($click->id)->update([
            'created_at' => now()->subMonth(),
        ]);

        $response = $this->actingAs($advertiser)
            ->getJson(route('advertiser.summary', [
                'period' => 'day',
            ]));

        $response
            ->assertOk()
            ->assertJson([
                'totalClicks' => 1,
                'totalExpenses' => 100.00,
            ]);

        $response = $this->actingAs($advertiser)
            ->getJson(route('advertiser.summary', [
                'period' => 'all',
            ]));

        $response
            ->assertOk()
            ->assertJson([
                'totalClicks' => 2,
                'totalExpenses' => 200.00,
            ]);
    }

    public function test_webmaster_summary_filters_clicks_sum_by_period(): void
    {

        $advertiser = User::factory()->advertiser()->create();

        $webmaster = User::factory()->webmaster()->create();

        $offer = Offer::factory()->create([
            'advertiser_id' => $advertiser->id,
        ]);

        $subscription = OfferSubscription::factory()->create([
            'offer_id' => $offer->id,
            'webmaster_id' => $webmaster->id,
        ]);

        // клик за сегодня
        OfferClick::create([
            'offer_id' => $offer->id,
            'advertiser_id' => $advertiser->id,
            'webmaster_id' => $webmaster->id,
            'subscription_id' => $subscription->id,
            'ref_code' => $subscription->ref_code,
            'target_url' => 'http://example.ru',
            'advertiser_cost' => 100,
            'webmaster_income' => 80,
            'system_commission' => 20,
        ]);

        // клик месяц назад
        $click = OfferClick::create([
            'offer_id' => $offer->id,
            'advertiser_id' => $advertiser->id,
            'webmaster_id' => $webmaster->id,
            'subscription_id' => $subscription->id,
            'ref_code' => $subscription->ref_code,
            'target_url' => 'http://example.ru',
            'advertiser_cost' => 100,
            'webmaster_income' => 80,
            'system_commission' => 20,
        ]);

        // изменяем дату у последнего клика
        OfferClick::whereKey($click->id)->update([
            'created_at' => now()->subMonth(),
        ]);

        $response = $this->actingAs($webmaster)
            ->getJson(route('webmaster.summary', [
                'period' => 'day',
            ]));

        $response
            ->assertOk()
            ->assertJson([
                'totalClicks' => 1,
                'totalRevenue' => 80.00,
            ]);

        $response = $this->actingAs($webmaster)
            ->getJson(route('webmaster.summary', [
                'period' => 'all',
            ]));

        $response
            ->assertOk()
            ->assertJson([
                'totalClicks' => 2,
                'totalRevenue' => 160.00,
            ]);
    }
}
