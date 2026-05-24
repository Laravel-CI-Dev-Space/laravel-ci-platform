<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Creates the platform roles.
     * Account suspension is handled via is_active and suspended_until — no inactive role needed.
     */
    public function run(): void
    {
        $roles = [
            'super-admin',
            'admin',
            'moderateur',
            'membre-actif',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        $this->command->info('Roles created successfully.');
    }
}
