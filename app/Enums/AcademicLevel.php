<?php

namespace App\Enums;

enum AcademicLevel: string
{
    case Bts              = 'bts';
    case Bachelor         = 'licence';
    case MasterOrEngineer = 'master_ingenieur';
    case Phd              = 'doctorat';

    public function label(): string
    {
        return match ($this) {
            self::Bts              => 'BTS',
            self::Bachelor         => 'Licence',
            self::MasterOrEngineer => 'Master / Ingénieur',
            self::Phd              => 'Doctorat',
        };
    }

    public static function labels(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($c) => [$c->value => $c->label()])
            ->all();
    }
}
