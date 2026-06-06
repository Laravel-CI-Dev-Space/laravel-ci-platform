<?php

namespace App\Models;

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
            'is_active' => 'boolean',
        ];
    }

    /** Retourne l'URL du média ou null selon le type. */
    public function mediaUrl(): ?string
    {
        return match ($this->media_type) {
            'image', 'video' => $this->media_path ? asset('assets/' . $this->media_path) : null,
            'youtube'        => $this->youtube_url ?: null,
            default          => null,
        };
    }

    /** Indique si le média est une vidéo YouTube. */
    public function isYoutube(): bool
    {
        return $this->media_type === 'youtube';
    }

    /** Indique si le média est une image. */
    public function isImage(): bool
    {
        return $this->media_type === 'image';
    }

    /** Indique si le média est une vidéo locale. */
    public function isVideo(): bool
    {
        return $this->media_type === 'video';
    }
}
