<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

#[Fillable(['key', 'group', 'value', 'type', 'label', 'description', 'order'])]
class SiteSetting extends Model
{
    /**
     * Récupère la valeur d'un paramètre par clé, en cache indéfiniment
     * (invalidée par `set()`). Évite des centaines de requêtes répétées,
     * notamment lors du recalcul de réputation de tous les membres.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $value = Cache::rememberForever(
            "site_setting:{$key}",
            fn () => static::where('key', $key)->value('value')
        );

        return $value ?? $default;
    }

    /**
     * Récupère tous les paramètres d'un groupe (mis en cache).
     * Le résultat est mis en cache sous forme de tableau (et non de
     * Collection de modèles) pour éviter les soucis de unserialize()
     * d'objets selon le driver de cache.
     */
    public static function getGroup(string $group): Collection
    {
        $rows = Cache::rememberForever(
            "site_setting_group:{$group}",
            fn () => static::where('group', $group)->orderBy('order')->get()->toArray()
        );

        return static::hydrate($rows);
    }

    /** Met à jour ou crée un paramètre par clé et invalide son cache. */
    public static function set(string $key, mixed $value): void
    {
        $setting = static::updateOrCreate(['key' => $key], ['value' => $value]);

        Cache::forget("site_setting:{$key}");
        Cache::forget("site_setting_group:{$setting->group}");
    }
}
