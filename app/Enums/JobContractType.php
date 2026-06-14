<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum JobContractType: string
{
    use HasOptions;

    case Cdi            = 'cdi';
    case Cdd            = 'cdd';
    case Freelance      = 'freelance';
    case Internship     = 'internship';
    case Apprenticeship = 'apprenticeship';

    public function label(): string
    {
        return match ($this) {
            self::Cdi            => 'CDI',
            self::Cdd            => 'CDD',
            self::Freelance      => 'Freelance',
            self::Internship     => 'Stage',
            self::Apprenticeship => 'Alternance',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Cdi            => 'success',
            self::Cdd            => 'info',
            self::Freelance      => 'warning',
            self::Internship     => 'gray',
            self::Apprenticeship => 'gray',
        };
    }
}
