<?php

declare(strict_types=1);

namespace App\Enums\Concerns;

/**
 * Fournit un tableau valeur => libellé à partir des cases de l'enum,
 * utilisable directement dans les Select/SelectFilter de Filament.
 */
trait HasOptions
{
    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return array_combine(
            array_map(fn (self $case) => $case->value, self::cases()),
            array_map(fn (self $case) => $case->label(), self::cases()),
        );
    }
}
