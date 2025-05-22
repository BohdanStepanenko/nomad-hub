<?php

namespace Database\Factories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Housing>
 */
class HousingFactory extends Factory
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
            'description' => fake()->text,
            'country_id' => fake()->randomElement($countries),
            'city' => fake()->city,
            'address' => fake()->address,
            'price' => fake()->randomFloat(2, 100, 1000),
        ];
    }
}
