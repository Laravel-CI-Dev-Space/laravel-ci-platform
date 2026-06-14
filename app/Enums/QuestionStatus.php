<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum QuestionStatus: string
{
    use HasOptions;

    case Published = 'published';
    case Hidden    = 'hidden';
    case Closed    = 'closed';
    case Deleted   = 'deleted';

    public function label(): string
    {
        return match ($this) {
            self::Published => 'Publiée',
            self::Hidden    => 'Masquée',
            self::Closed    => 'Fermée',
            self::Deleted   => 'Supprimée',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Published => 'success',
            self::Hidden    => 'warning',
            self::Closed    => 'gray',
            self::Deleted   => 'danger',
        };
    }
}
