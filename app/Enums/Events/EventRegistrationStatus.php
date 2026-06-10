<?php

namespace App\Enums\Events;

use App\Contracts\HasLabel;
use App\Enums\Concerns\HasOptions;

enum EventRegistrationStatus: string implements HasLabel
{
    use HasOptions;
    case PENDING   = 'pending';
    case CONFIRMED = 'confirmed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING   => 'En attente',
            self::CONFIRMED => 'Confirmée',
            self::CANCELLED => 'Annulée',
        };
    }
}
