<?php

declare(strict_types=1);

namespace App\Enums;

enum MediaType: string
{
    case Image   = 'image';
    case Video   = 'video';
    case Youtube = 'youtube';
    case None    = 'none';

    public function label(): string
    {
        return match ($this) {
            self::Image   => 'Image',
            self::Video   => 'Vidéo',
            self::Youtube => 'YouTube',
            self::None    => 'Aucun',
        };
    }
}
