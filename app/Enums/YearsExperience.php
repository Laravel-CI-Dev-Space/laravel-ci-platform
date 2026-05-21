<?php

namespace App\Enums;

enum YearsExperience: string
{
    case LessThanOne  = 'moins_1_an';
    case OneToThree   = '1_3_ans';
    case ThreeToFive  = '3_5_ans';
    case FiveToTen    = '5_10_ans';
    case MoreThanTen  = 'plus_10_ans';

    public function label(): string
    {
        return match ($this) {
            self::LessThanOne => "Moins d'1 an",
            self::OneToThree  => '1 à 3 ans',
            self::ThreeToFive => '3 à 5 ans',
            self::FiveToTen   => '5 à 10 ans',
            self::MoreThanTen => 'Plus de 10 ans',
        };
    }

    public static function labels(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($c) => [$c->value => $c->label()])
            ->all();
    }
}
