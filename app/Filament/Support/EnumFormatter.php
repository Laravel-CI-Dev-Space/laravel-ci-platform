<?php

declare(strict_types=1);

namespace App\Filament\Support;

use App\Contracts\HasLabel;

final class EnumFormatter
{
    public static function label(mixed $state): string
    {
        if ($state instanceof HasLabel) {
            return $state->label();
        }

        return (string) ($state ?? '—');
    }
}
