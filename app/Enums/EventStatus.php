<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum EventStatus: string
{
    use HasOptions;

    case Draft     = 'draft';
    case Published = 'published';
    case Cancelled = 'cancelled';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::Draft     => 'Brouillon',
            self::Published => 'Publié',
            self::Cancelled => 'Annulé',
            self::Completed => 'Terminé',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft     => 'gray',
            self::Published => 'success',
            self::Cancelled => 'danger',
            self::Completed => 'info',
        };
    }
}
