<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\UserPermission;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ── 1. Crée toutes les permissions depuis l'enum ──────────
        // firstOrCreate = safe sur re-seed : ne crée que les nouvelles.
        foreach (UserPermission::cases() as $permission) {
            Permission::firstOrCreate([
                'name'       => $permission->value,
                'guard_name' => 'web',
            ]);
        }

        // ── 2. Crée les rôles et synchronise leurs permissions ─────
        // syncPermissions() = idempotent : ajoute les nouvelles,
        // retire celles supprimées du tableau, ne touche pas aux autres rôles.
        foreach (UserRole::cases() as $role) {
            $roleModel = Role::firstOrCreate([
                'name'       => $role->value,
                'guard_name' => 'web',
            ]);

            $roleModel->syncPermissions(
                UserPermission::forRole($role)
            );
        }

        $this->command->info('✅ Rôles et permissions seedés via les enums.');
    }
}
