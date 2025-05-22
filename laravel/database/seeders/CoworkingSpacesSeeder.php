<?php

namespace Database\Seeders;

use App\Models\CoworkingSpace;
use Illuminate\Database\Seeder;

class CoworkingSpacesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CoworkingSpace::factory(5)->create();
    }
}
