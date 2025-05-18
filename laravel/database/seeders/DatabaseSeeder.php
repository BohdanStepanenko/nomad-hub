<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesSeeder::class,
            FirstAdminSeeder::class,
            CountriesSeeder::class,
            VisasSeeder::class,
            FirstUserSeeder::class,
            CoworkingSpacesSeeder::class,
            CoworkingReviewsSeeder::class,
        ]);
    }
}
