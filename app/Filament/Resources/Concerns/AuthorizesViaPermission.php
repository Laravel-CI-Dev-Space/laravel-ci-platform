<?php

declare(strict_types=1);

namespace App\Filament\Resources\Concerns;

use App\Enums\UserPermission;

/**
 * Authorise l'accès aux ressources Filament via le système de permissions
 * Spatie plutôt que via les rôles directement. Un super-admin peut ainsi
 * accorder ou retirer l'accès à une ressource à n'importe quel utilisateur,
 * indépendamment de son rôle, en (dé)synchronisant ses permissions directes
 * (cf. UserResource > Permissions individuelles).
 */
trait AuthorizesViaPermission
{
    /**
     * Permission requise pour voir la liste / consulter la ressource.
     * Surchargez cette méthode dans la ressource pour exiger une autre permission.
     */
    protected static function viewPermission(): string
    {
        return UserPermission::AdminAccess->value;
    }

    /**
     * Permission requise pour créer/modifier/supprimer.
     * Par défaut identique à `viewPermission()`.
     */
    protected static function managePermission(): string
    {
        return static::viewPermission();
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can(static::viewPermission()) ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can(static::managePermission()) ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can(static::managePermission()) ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can(static::managePermission()) ?? false;
    }
}
