<?php

namespace Database\Seeders;

use App\Models\ForumTopic;
use App\Models\User;
use Illuminate\Database\Seeder;

class ForumTopicsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        if ($user) {
            ForumTopic::factory()->count(3)->create([
                'user_id' => $user->id,
            ]);
        }
    }
}
