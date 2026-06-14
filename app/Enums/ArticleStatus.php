<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum ArticleStatus: string
{
    use HasOptions;

    case Draft     = 'draft';
    case Pending   = 'pending';
    case Published = 'published';
    case Rejected  = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Draft     => 'Brouillon',
            self::Pending   => 'En attente',
            self::Published => 'Publié',
            self::Rejected  => 'Rejeté',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft     => 'gray',
            self::Pending   => 'warning',
            self::Published => 'success',
            self::Rejected  => 'danger',
        };
    }
}
