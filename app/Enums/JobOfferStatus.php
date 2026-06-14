<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum JobOfferStatus: string
{
    use HasOptions;

    case Draft    = 'draft';
    case Pending  = 'pending';
    case Active   = 'active';
    case Expired  = 'expired';
    case Rejected = 'rejected';
    case Filled   = 'filled';

    public function label(): string
    {
        return match ($this) {
            self::Draft    => 'Brouillon',
            self::Pending  => 'En attente',
            self::Active   => 'Active',
            self::Expired  => 'Expirée',
            self::Rejected => 'Rejetée',
            self::Filled   => 'Pourvue',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft    => 'gray',
            self::Pending  => 'warning',
            self::Active   => 'success',
            self::Expired  => 'gray',
            self::Rejected => 'danger',
            self::Filled   => 'info',
        };
    }
}
