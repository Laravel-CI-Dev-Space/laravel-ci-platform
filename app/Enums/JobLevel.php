<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum JobLevel: string
{
    use HasOptions;

    case Junior       = 'junior';
    case Intermediate = 'intermediate';
    case Senior       = 'senior';
    case Lead         = 'lead';
    case Any          = 'any';

    public function label(): string
    {
        return match ($this) {
            self::Junior       => 'Junior',
            self::Intermediate => 'Intermédiaire',
            self::Senior       => 'Senior',
            self::Lead         => 'Lead',
            self::Any          => 'Tous niveaux',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Junior       => 'info',
            self::Intermediate => 'warning',
            self::Senior       => 'success',
            self::Lead         => 'danger',
            self::Any          => 'gray',
        };
    }
}
