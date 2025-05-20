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
            FirstUserSeeder::class,

            CountriesSeeder::class,
            VisasSeeder::class,

            CoworkingSpacesSeeder::class,
            CoworkingReviewsSeeder::class,

            ForumTopicsSeeder::class,
            ForumPostsSeeder::class,
            ForumCommentsSeeder::class,

            TaxInfosSeeder::class,

            HousingsSeeder::class,
            FavoriteHousingsSeeder::class,
        ]);
    }
}
