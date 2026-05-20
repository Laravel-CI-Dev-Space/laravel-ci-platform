<?php

namespace App\Enums\Events;

use App\Contracts\HasLabel;
use App\Enums\Concerns\HasOptions;

enum EventReminderType: string implements HasLabel
{
    use HasOptions;
    case J_7 = 'J-7';
    case J_1 = 'J-1';
    case H_1 = 'H-1';

    public function label(): string
    {
        return match ($this) {
            self::J_7 => '7 jours avant',
            self::J_1 => '1 jour avant',
            self::H_1 => '1 heure avant',
        };
    }
}
