<?php

namespace App\Enums\Jobs;

use App\Contracts\HasLabel;
use App\Enums\Concerns\HasOptions;

enum JobOfferType: string implements HasLabel
{
    use HasOptions;
    case CDI = 'cdi';
    case FREELANCE = 'freelance';
    case REMOTE = 'remote';
    case STAGE = 'stage';

    public function label(): string
    {
        return match ($this) {
            self::CDI => 'CDI',
            self::FREELANCE => 'Freelance',
            self::REMOTE => 'Télétravail',
            self::STAGE => 'Stage',
        };
    }
}
