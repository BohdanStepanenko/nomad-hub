<?php

namespace Database\Factories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CoworkingSpace>
 */
class CoworkingSpaceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $countries = Country::all();

        return [
            'name' => fake()->word,
            'country_id' => fake()->randomElement($countries),
            'city' => fake()->city,
            'address' => fake()->address,
            'hours' => '9:00-18:00',
            'cost' => fake()->randomFloat(2, 10, 100),
            'wifi_speed' => '100 Mbps',
            'has_coffee' => fake()->boolean,
            'is_24_7' => fake()->boolean,
            'website' => fake()->url,
        ];
    }
}
