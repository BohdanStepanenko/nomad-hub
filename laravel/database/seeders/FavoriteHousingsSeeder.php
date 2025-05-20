<?php

namespace Database\Seeders;

use App\Models\FavoriteHousing;
use App\Models\Housing;
use App\Models\User;
use Illuminate\Database\Seeder;

class FavoriteHousingsSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $housing = Housing::first();

        if ($user && $housing) {
            FavoriteHousing::factory()->create([
                'user_id' => $user->id,
                'housing_id' => $housing->id,
            ]);
        }
    }
}
