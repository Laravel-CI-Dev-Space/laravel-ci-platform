<?php

namespace App\Observers;

use App\Enums\UserRole;
use App\Models\PoleMember;

class PoleMemberObserver
{
    public function saved(PoleMember $poleMember): void
    {
        $this->syncPoleRole($poleMember);
    }

    public function deleted(PoleMember $poleMember): void
    {
        $this->revokePoleRole($poleMember);
    }

    private function syncPoleRole(PoleMember $poleMember): void
    {
        $user = $poleMember->user;

        if ($user === null) {
            return;
        }

        // Révoquer tous les rôles pôle existants avant d'en assigner un nouveau
        $user->removeRole(
            array_values(array_filter(
                UserRole::poleRoleValues(),
                fn (string $role) => $user->hasRole($role),
            ))
        );

        if ($poleMember->isActif()) {
            $role = $this->resolveRole($poleMember);

            if ($role !== null) {
                $user->assignRole($role->value);
            }
        }
    }

    private function revokePoleRole(PoleMember $poleMember): void
    {
        $user = $poleMember->user;

        if ($user === null) {
            return;
        }

        $user->removeRole(UserRole::poleRoleValues());
    }

    private function resolveRole(PoleMember $poleMember): ?UserRole
    {
        $slug = $poleMember->pole?->slug;

        if ($slug === null) {
            return null;
        }

        return UserRole::tryFrom("pole-{$slug}");
    }
}
