<?php

namespace App\Enums\Events;

use App\Contracts\HasLabel;
use App\Enums\Concerns\HasOptions;

/**
 * Reminder slot identifiers stored in event_registrations.reminder_types (JSON).
 * J-7 = 7 days before, J-1 = 1 day before, H-1 = 1 hour before event start.
 */
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
