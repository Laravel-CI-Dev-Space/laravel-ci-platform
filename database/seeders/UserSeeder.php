<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Creates the default super-admin account.
     * Update the values below with the actual GitHub account details before running.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'wilson@laravelci.com'],
            [
                'name'              => 'Kouassi Yanne Cedric Wilson',
                'avatar'            => 'https://avatars.githubusercontent.com/u/167759591?v=4',
                'github_id'         => '167759591',
                'github_username'   => 'Ky-Wilson',
                'is_active'         => true,
                'email_verified_at' => now(),
            ]
        );

        $admin->assignRole('super-admin');

        $this->command->info('Super admin created successfully.');
    }
}
