<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\TaxInfo;
use Illuminate\Database\Seeder;

class TaxInfosSeeder extends Seeder
{
    public function run(): void
    {
        $country = Country::first();

        if ($country) {
            TaxInfo::factory()->count(3)->create([
                'country_id' => $country->id,
            ]);
        }
    }
}
