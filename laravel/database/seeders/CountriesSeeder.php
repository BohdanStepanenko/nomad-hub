<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            ['name' => 'Ukraine', 'code' => 'UA'],
            ['name' => 'Thailand', 'code' => 'TH'],
            ['name' => 'Portugal', 'code' => 'PT'],
            ['name' => 'Germany', 'code' => 'DE'],
        ];

        foreach ($countries as $country) {
            Country::firstOrCreate($country);
        }
    }
}
