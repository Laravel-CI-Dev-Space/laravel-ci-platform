<?php

namespace App\Enums\Events;

use App\Contracts\HasLabel;
use App\Enums\Concerns\HasOptions;

enum EventStatus: string implements HasLabel
{
    use HasOptions;
    case DRAFT     = 'draft';
    case PUBLISHED = 'published';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT     => 'Brouillon',
            self::PUBLISHED => 'Publié',
            self::CANCELLED => 'Annulé',
        };
    }
}
