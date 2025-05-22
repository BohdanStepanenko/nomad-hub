<?php

namespace Database\Factories;

use App\Models\CoworkingSpace;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CoworkingReview>
 */
class CoworkingReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $coworkingSpaces = CoworkingSpace::all();
        $users = User::all();

        return [
            'coworking_space_id' => fake()->randomElement($coworkingSpaces),
            'user_id' => fake()->randomElement($users),
            'rating' => rand(1, 5),
            'comment' => fake()->text,
        ];
    }
}
