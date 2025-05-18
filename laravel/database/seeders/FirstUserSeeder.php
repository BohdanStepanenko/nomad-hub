<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FirstUserSeeder extends Seeder
{
    const USER_NAME = 'First Admin';

    const USER_EMAIL = 'user@email.com';

    const USER_PASSWORD = 'password';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::factory()
            ->create([
                'name' => self::USER_NAME,
                'email' => self::USER_EMAIL,
                'password' => Hash::make(self::USER_PASSWORD),
            ]);

        $admin->assignRole('Client');
    }
}
