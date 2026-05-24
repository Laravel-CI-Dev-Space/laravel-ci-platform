<?php

namespace App\Enums;

enum JobStatus: string
{
    case Employed     = 'en_fonction';
    case Student      = 'etudiant';
    case Entrepreneur = 'entrepreneur';
    case JobSeeking   = 'recherche_emploi';
    case Freelance    = 'freelance';

    public function label(): string
    {
        return match ($this) {
            self::Employed     => 'En fonction',
            self::Student      => 'Étudiant',
            self::Entrepreneur => 'Entrepreneur',
            self::JobSeeking   => "En recherche d'emploi",
            self::Freelance    => 'Freelance',
        };
    }

    public static function labels(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($c) => [$c->value => $c->label()])
            ->all();
    }
}
