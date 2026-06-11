<?php

declare(strict_types=1);

namespace App\Support;

use App\Enums\Events\EventMediaType;
use Illuminate\Validation\ValidationException;

final class EventMediaFormData
{
    /** @param  array<string, mixed>  $data */
    public static function hydrate(array $data): array
    {
        $type = self::resolveType($data['type'] ?? null);

        if ($type === EventMediaType::VIDEO) {
            $data['video_url'] = $data['url'] ?? null;
        } elseif ($type === EventMediaType::IMAGE) {
            $data['image_path'] = $data['url'] ?? null;
        } elseif ($type === EventMediaType::PDF) {
            $data['document_path'] = $data['url'] ?? null;
        }

        return $data;
    }

    /** @param  array<string, mixed>  $data */
    public static function prepareForSave(array $data): array
    {
        $type = self::resolveType($data['type'] ?? null);

        $data['url'] = match ($type) {
            EventMediaType::VIDEO => self::stringOrNull($data['video_url'] ?? null)            ?? self::stringOrNull($data['url'] ?? null),
            EventMediaType::IMAGE => self::normalizeUploadPath($data['image_path'] ?? null)    ?? self::stringOrNull($data['url'] ?? null),
            EventMediaType::PDF   => self::normalizeUploadPath($data['document_path'] ?? null) ?? self::stringOrNull($data['url'] ?? null),
            default               => self::stringOrNull($data['url'] ?? null),
        };

        unset($data['video_url'], $data['image_path'], $data['document_path']);

        if (! filled($data['url'])) {
            throw ValidationException::withMessages([
                'media' => 'Chaque média doit avoir une URL ou un fichier.',
            ]);
        }

        return $data;
    }

    private static function resolveType(mixed $type): ?EventMediaType
    {
        if ($type instanceof EventMediaType) {
            return $type;
        }

        return is_string($type) ? EventMediaType::tryFrom($type) : null;
    }

    private static function normalizeUploadPath(mixed $path): ?string
    {
        if (is_array($path)) {
            $path = $path[array_key_first($path)] ?? null;
        }

        return self::stringOrNull($path);
    }

    private static function stringOrNull(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value !== '' ? $value : null;
    }
}
