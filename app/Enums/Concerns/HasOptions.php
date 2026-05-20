<?php

namespace App\Enums\Concerns;

use App\Contracts\HasLabel;
use BackedEnum;

trait HasOptions
{
    /**
     * @return array<string, string> value => label
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (BackedEnum&HasLabel $case) => [$case->value => $case->label()])
            ->all();
    }
}
