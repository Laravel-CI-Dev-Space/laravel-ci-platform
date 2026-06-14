<?php

namespace App\Models;

use App\Enums\MediaType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'eyebrow', 'title', 'content', 'media_type', 'media_path',
    'youtube_url', 'media_position', 'caption', 'is_active',
])]
class AboutOriginSection extends Model
{
    protected function casts(): array
    {
        return [
            'is_active'  => 'boolean',
            'media_type' => MediaType::class,
        ];
    }

    /** Retourne l'URL du média ou null selon le type. */
    public function mediaUrl(): ?string
    {
        return match ($this->media_type) {
            MediaType::Image, MediaType::Video => $this->media_path ? asset('assets/' . $this->media_path) : null,
            MediaType::Youtube                 => $this->youtube_url ?: null,
            default                            => null,
        };
    }

    /** Indique si le média est une vidéo YouTube. */
    public function isYoutube(): bool
    {
        return $this->media_type === MediaType::Youtube;
    }

    /** Indique si le média est une image. */
    public function isImage(): bool
    {
        return $this->media_type === MediaType::Image;
    }

    /** Indique si le média est une vidéo locale. */
    public function isVideo(): bool
    {
        return $this->media_type === MediaType::Video;
    }
}
