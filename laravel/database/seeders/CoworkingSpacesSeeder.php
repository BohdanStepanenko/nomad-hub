<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\CoworkingSpace;
use Illuminate\Database\Seeder;

class CoworkingSpacesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $thailand = Country::where('code', 'TH')->first();

        if (!$thailand) {
            $thailand = Country::create([
                'name' => 'Thailand',
                'code' => 'TH',
            ]);
        }

        CoworkingSpace::create([
            'name' => 'Hubba Thailand',
            'address' => '123 Sukhumvit Rd',
            'city' => 'Bangkok',
            'country_id' => $thailand->id,
            'hours' => '9:00-18:00',
            'cost' => 15.00,
            'wifi_speed' => '100 Mbps',
            'has_coffee' => true,
            'is_24_7' => false,
            'website' => 'https://www.hubbathailand.com/',
        ]);
    }
}
