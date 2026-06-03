<?php

declare(strict_types=1);

namespace App\Enums\Forum;

enum QuestionStatus: string
{
    case Published = 'published';
    case Hidden    = 'hidden';
    case Closed    = 'closed';
    case Deleted   = 'deleted';

    public function label(): string
    {
        return match ($this) {
            self::Published => 'Publié',
            self::Hidden    => 'Masqué',
            self::Closed    => 'Fermé',
            self::Deleted   => 'Supprimé',
        };
    }
}
