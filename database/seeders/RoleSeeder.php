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

        // ── 1. Supprime les rôles obsolètes (ex: admin, moderator) ───────
        $validRoleValues = UserRole::values();
        Role::whereNotIn('name', $validRoleValues)->get()->each(function (Role $role) {
            $role->users()->detach();
            $role->permissions()->detach();
            $role->delete();
        });

        // ── 2. Supprime les permissions obsolètes ────────────────────────
        $validPermissionValues = UserPermission::values();
        Permission::whereNotIn('name', $validPermissionValues)->get()->each(function (Permission $permission) {
            $permission->roles()->detach();
            $permission->delete();
        });

        // ── 3. Crée toutes les permissions depuis l'enum ─────────────────
        foreach (UserPermission::cases() as $permission) {
            Permission::firstOrCreate([
                'name'       => $permission->value,
                'guard_name' => 'web',
            ]);
        }

        // ── 4. Crée les rôles et synchronise leurs permissions ───────────
        foreach (UserRole::cases() as $role) {
            $roleModel = Role::firstOrCreate([
                'name'       => $role->value,
                'guard_name' => 'web',
            ]);

            $roleModel->syncPermissions(
                UserPermission::forRole($role)
            );
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('Roles et permissions seedés via les enums.');
    }
}
