<?php

namespace App\Enums;

enum LaravelLevel: string
{
    case Beginner     = 'debutant';
    case Intermediate = 'intermediaire';
    case Advanced     = 'avance';
    case Expert       = 'expert';
    case Master       = 'maitre';

    public function label(): string
    {
        return match ($this) {
            self::Beginner     => 'Débutant (moins de 5 projets)',
            self::Intermediate => 'Intermédiaire (6 à 14 projets)',
            self::Advanced     => 'Avancé (15 à 25 projets)',
            self::Expert       => 'Expert (26 à 40 projets)',
            self::Master       => 'Maître (plus de 50 projets)',
        };
    }

    public static function labels(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($c) => [$c->value => $c->label()])
            ->all();
    }
}
