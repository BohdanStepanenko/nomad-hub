<?php

namespace Database\Factories;

use App\Models\ForumPost;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ForumComment>
 */
class ForumCommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'forum_post_id' => ForumPost::factory(),
            'user_id' => User::factory(),
            'content' => fake()->paragraph(),
        ];
    }
}
