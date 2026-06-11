<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Facades\Storage;

final class MediaUrl
{
    public static function resolve(mixed $path): ?string
    {
        $path = self::normalizePath($path);

        if ($path === null) {
            return null;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        $path = ltrim($path, '/');

        if (str_starts_with($path, 'storage/')) {
            return url($path);
        }

        if (str_starts_with($path, 'assets/')) {
            return asset($path);
        }

        return Storage::disk('public')->url($path);
    }

    private static function normalizePath(mixed $path): ?string
    {
        if (is_array($path)) {
            $path = $path[array_key_first($path)] ?? null;
        }

        if (! is_string($path)) {
            return null;
        }

        $path = trim($path);

        if ($path === '') {
            return null;
        }

        if (str_starts_with($path, '[')) {
            $decoded = json_decode($path, true);

            if (is_array($decoded)) {
                return self::normalizePath($decoded);
            }
        }

        return $path;
    }
}
