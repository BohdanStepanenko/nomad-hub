<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FirstAdminSeeder extends Seeder
{
    const ADMIN_NAME = 'First Admin';

    const ADMIN_EMAIL = 'admin@email.com';

    const ADMIN_PASSWORD = 'password';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::factory()
            ->create([
                'name' => self::ADMIN_NAME,
                'email' => self::ADMIN_EMAIL,
                'password' => Hash::make(self::ADMIN_PASSWORD),
            ]);

        $admin->assignRole('Admin');
    }
}
