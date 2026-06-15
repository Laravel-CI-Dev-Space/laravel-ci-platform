<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Met en cache indéfiniment le résultat du scope `active()` (contenu
 * éditorial rarement modifié, affiché sur les pages publiques à chaque
 * requête) et invalide automatiquement ce cache à la sauvegarde ou
 * suppression d'un enregistrement.
 */
trait CachesActiveRecords
{
    protected static function activeCacheKey(): string
    {
        return Str::snake(class_basename(static::class)) . ':active';
    }

    public static function cachedActive(): Collection
    {
        $rows = Cache::rememberForever(
            static::activeCacheKey(),
            fn () => static::active()->get()->toArray()
        );

        return static::hydrate($rows);
    }

    protected static function bootCachesActiveRecords(): void
    {
        static::saved(fn () => Cache::forget(static::activeCacheKey()));
        static::deleted(fn () => Cache::forget(static::activeCacheKey()));
    }
}
