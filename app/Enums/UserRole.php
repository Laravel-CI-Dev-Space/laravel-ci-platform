<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin          = 'super-admin';
    case Member              = 'member';
    case Company             = 'company';
    case PoleCommunication   = 'pole-communication';
    case PoleEvenements      = 'pole-evenements';
    case PoleTechFormation   = 'pole-tech-formation';
    case PoleEmployabilite   = 'pole-employabilite';
    case PolePartenariat     = 'pole-partenariat';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin        => 'Super Administrateur',
            self::Member            => 'Membre',
            self::Company           => 'Entreprise',
            self::PoleCommunication => 'Gestionnaire — Pôle Communication',
            self::PoleEvenements    => 'Gestionnaire — Pôle Événements',
            self::PoleTechFormation => 'Gestionnaire — Pôle Tech & Formation',
            self::PoleEmployabilite => 'Gestionnaire — Pôle Employabilité',
            self::PolePartenariat   => 'Gestionnaire — Pôle Partenariat',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function poleRoles(): array
    {
        return [
            self::PoleCommunication,
            self::PoleEvenements,
            self::PoleTechFormation,
            self::PoleEmployabilite,
            self::PolePartenariat,
        ];
    }

    public static function poleRoleValues(): array
    {
        return array_map(fn (self $r) => $r->value, self::poleRoles());
    }
}
