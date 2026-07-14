<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Offer;
use App\Models\OfferTheme;

/**
 * @extends Factory<Offer>
 */
class OfferFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
            'url' => fake()->unique()->url(),
            'price' => fake()->numberBetween(100, 110),
            'theme_id' => OfferTheme::factory(),
            'advertiser_id' => User::factory()->advertiser(),
            'status' => 0,
        ];
    }
}
