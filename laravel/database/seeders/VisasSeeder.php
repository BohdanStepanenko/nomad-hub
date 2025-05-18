<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Visa;
use Illuminate\Database\Seeder;

class VisasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $thailand = Country::where('code', 'TH')->first();
        $portugal = Country::where('code', 'PT')->first();

        if ($thailand) {
            Visa::create([
                'country_id' => $thailand->id,
                'visa_type' => 'Tourist Visa',
                'duration' => 60,
                'requirements' => 'Passport valid for at least 6 months, photo, application form',
                'cost' => 30.00,
                'source' => 'https://www.thaievisa.go.th/',
            ]);
        }

        if ($portugal) {
            Visa::create([
                'country_id' => $portugal->id,
                'visa_type' => 'D7 Visa',
                'duration' => 365,
                'requirements' => 'Proof of income, health insurance, accommodation',
                'cost' => 90.00,
                'source' => 'https://www.portugal.gov.pt/',
            ]);
        }
    }
}
