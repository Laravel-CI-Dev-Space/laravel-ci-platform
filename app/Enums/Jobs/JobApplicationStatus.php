<?php

namespace App\Enums\Jobs;

use App\Contracts\HasLabel;
use App\Enums\Concerns\HasOptions;

enum JobApplicationStatus: string implements HasLabel
{
    use HasOptions;
    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'En attente',
            self::ACCEPTED => 'Acceptée',
            self::REJECTED => 'Refusée',
        };
    }
}
