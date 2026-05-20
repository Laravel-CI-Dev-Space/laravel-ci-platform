<?php

namespace App\Enums\Events;

use App\Contracts\HasLabel;
use App\Enums\Concerns\HasOptions;

enum EventMediaType: string implements HasLabel
{
    use HasOptions;
    case IMAGE = 'image';
    case VIDEO = 'video';
    case PDF = 'pdf';

    /**
     * Return the label of the media type.
     */
    public function label(): string
    {
        return match ($this) {
            self::IMAGE => 'Image',
            self::VIDEO => 'Vidéo',
            self::PDF => 'PDF',
        };
    }
}
