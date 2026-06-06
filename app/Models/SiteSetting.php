<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['key', 'group', 'value', 'type', 'label', 'description', 'order'])]
class SiteSetting extends Model
{
    /** Récupère la valeur d'un paramètre par clé. */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();

        return $setting ? $setting->value : $default;
    }

    /** Récupère tous les paramètres d'un groupe. */
    public static function getGroup(string $group): Collection
    {
        return static::where('group', $group)->orderBy('order')->get();
    }

    /** Met à jour ou crée un paramètre par clé. */
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
