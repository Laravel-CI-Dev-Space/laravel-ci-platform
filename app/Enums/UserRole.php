<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin = 'super-admin';
    case Admin      = 'admin';
    case Moderator  = 'moderator';
    case Member     = 'member';
    case Company    = 'company';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Administrateur',
            self::Admin      => 'Administrateur',
            self::Moderator  => 'Modérateur',
            self::Member     => 'Membre',
            self::Company    => 'Entreprise',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
