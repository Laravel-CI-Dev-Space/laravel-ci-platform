<?php

namespace App\Enums\Jobs;

use App\Contracts\HasLabel;
use App\Enums\Concerns\HasOptions;

enum JobOfferStatus: string implements HasLabel
{
    use HasOptions;
    case ACTIVE  = 'active';
    case EXPIRED = 'expired';
    case DRAFT   = 'draft';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE  => 'Active',
            self::EXPIRED => 'Expirée',
            self::DRAFT   => 'Brouillon',
        };
    }
}
