<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Crée un super admin de test.
     * Remplacer les valeurs par vos vraies infos GitHub.
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

        $this->command->info('✅ Super Admin créé avec succès.');
    }
}
