<?php

declare(strict_types=1);

namespace App\Enums;

enum ReportStatus: string
{
    case Pending  = 'pending';
    case Reviewed = 'reviewed';
    case Resolved = 'resolved';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending  => 'En attente',
            self::Reviewed => 'Examiné',
            self::Resolved => 'Résolu',
            self::Rejected => 'Rejeté',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending  => 'warning',
            self::Reviewed => 'info',
            self::Resolved => 'success',
            self::Rejected => 'gray',
        };
    }
}
