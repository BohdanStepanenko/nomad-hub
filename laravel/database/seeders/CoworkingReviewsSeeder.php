<?php

namespace Database\Seeders;

use App\Models\CoworkingReview;
use App\Models\CoworkingSpace;
use App\Models\User;
use Illuminate\Database\Seeder;

class CoworkingReviewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coworking = CoworkingSpace::where('name', 'Hubba Thailand')->first();
        $user = User::first();

        if ($coworking && $user) {
            CoworkingReview::create([
                'coworking_space_id' => $coworking->id,
                'user_id' => $user->id,
                'rating' => 4,
                'comment' => 'Great place, fast Wi-Fi, but a bit noisy.',
            ]);
        }
    }
}
