<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum JobApplicationStatus: string
{
    use HasOptions;

    case Pending     = 'pending';
    case Viewed      = 'viewed';
    case Shortlisted = 'shortlisted';
    case Accepted    = 'accepted';
    case Rejected    = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending     => 'En attente',
            self::Viewed      => 'Vue',
            self::Shortlisted => 'Présélectionnée',
            self::Accepted    => 'Acceptée',
            self::Rejected    => 'Rejetée',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending     => 'gray',
            self::Viewed      => 'info',
            self::Shortlisted => 'warning',
            self::Accepted    => 'success',
            self::Rejected    => 'danger',
        };
    }
}
