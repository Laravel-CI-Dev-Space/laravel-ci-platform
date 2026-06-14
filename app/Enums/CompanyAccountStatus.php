<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum CompanyAccountStatus: string
{
    use HasOptions;

    case Pending   = 'pending';
    case Active    = 'active';
    case Suspended = 'suspended';
    case Rejected  = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending   => 'En attente',
            self::Active    => 'Actif',
            self::Suspended => 'Suspendu',
            self::Rejected  => 'Rejeté',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending   => 'warning',
            self::Active    => 'success',
            self::Suspended => 'danger',
            self::Rejected  => 'gray',
        };
    }
}
