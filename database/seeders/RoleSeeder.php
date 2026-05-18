<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Crée les rôles de base de la plateforme.
     * membre-inactif retiré — géré par is_active et suspended_until
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

        $this->command->info('✅ Rôles créés avec succès.');
    }
}
