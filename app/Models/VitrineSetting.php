<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class VitrineSetting extends Model
{
    protected $fillable = ['code', 'type', 'group', 'section', 'value'];

    protected function casts(): array
    {
        return [
            'value' => 'string',
        ];
    }

    public static function get(string $code, mixed $default = null): mixed
    {
        $setting = static::where('code', $code)->first();

        if (! $setting) {
            return $default;
        }

        return static::castValue($setting->value, $setting->type);
    }

    /** @return array<string, mixed> */
    public static function getGroup(string $group): array
    {
        $ttl = (int) config('vitrine.cache_ttl', 3600);

        if ($ttl <= 0) {
            return static::fetchGroup($group);
        }

        return Cache::remember("vitrine_group_{$group}", $ttl, fn () => static::fetchGroup($group));
    }

    /** @return array<string, mixed> */
    public static function getSection(string $group, string $section): array
    {
        $ttl = (int) config('vitrine.cache_ttl', 3600);

        $fetch = fn () => static::where('group', $group)
            ->where('section', $section)
            ->get()
            ->mapWithKeys(fn ($s) => [$s->code => static::castValue($s->value, $s->type)])
            ->all();

        if ($ttl <= 0) {
            return $fetch();
        }

        return Cache::remember("vitrine_{$group}_{$section}", $ttl, $fetch);
    }

    public static function forgetGroup(string $group): void
    {
        Cache::forget("vitrine_group_{$group}");
    }

    public static function forgetAll(): void
    {
        foreach (['home', 'about', 'global'] as $group) {
            Cache::forget("vitrine_group_{$group}");
        }
    }

    /** @return array<string, mixed> */
    private static function fetchGroup(string $group): array
    {
        return static::where('group', $group)
            ->get()
            ->mapWithKeys(fn ($s) => [$s->code => static::castValue($s->value, $s->type)])
            ->all();
    }

    private static function castValue(mixed $value, string $type): mixed
    {
        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'number'  => is_numeric($value) ? (int) $value : 0,
            'json'    => json_decode((string) $value, true) ?? [],
            default   => $value,
        };
    }
}
