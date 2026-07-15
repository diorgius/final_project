<?php

namespace Database\Factories;

use App\Models\OfferSubscription;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Offer;
use Illuminate\Support\Str;

/**
 * @extends Factory<OfferSubscription>
 */
class OfferSubscriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'offer_id' => Offer::factory(),
            'webmaster_id' => User::factory()->webmaster(),
            'ref_code' => Str::random(16),
        ];
    }
}
