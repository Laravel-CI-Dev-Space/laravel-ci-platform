<?php

declare(strict_types=1);

namespace App\Enums;

enum EventMediaType: string
{
    case Photo = 'photo';
    case Video = 'video';

    public function label(): string
    {
        return match ($this) {
            self::Photo => 'Photo',
            self::Video => 'Vidéo',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Photo => 'heroicon-o-photo',
            self::Video => 'heroicon-o-video-camera',
        };
    }

    public function acceptedMimeTypes(): array
    {
        return match ($this) {
            self::Photo => ['image/jpeg', 'image/png', 'image/webp', 'image/gif'],
            self::Video => ['video/mp4'],
        };
    }
}
