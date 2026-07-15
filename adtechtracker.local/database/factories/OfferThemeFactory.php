<?php

namespace Database\Factories;

use App\Models\OfferTheme;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OfferTheme>
 */
class OfferThemeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(2),
        ];
    }
}
