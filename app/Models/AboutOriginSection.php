<?php

namespace App\Models;

use App\Enums\MediaType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

#[Fillable([
    'eyebrow', 'title', 'content', 'media_type', 'media_path',
    'youtube_url', 'media_position', 'caption', 'is_active',
])]
class AboutOriginSection extends Model
{
    private const CACHE_KEY = 'about_origin_section:active';

    protected function casts(): array
    {
        return [
            'is_active'  => 'boolean',
            'media_type' => MediaType::class,
        ];
    }

    /** Retourne la section "origines" active (mise en cache indéfiniment). */
    public static function cachedActive(): ?self
    {
        $row = Cache::rememberForever(
            self::CACHE_KEY,
            fn () => static::where('is_active', true)->first()?->toArray()
        );

        return $row ? static::hydrate([$row])->first() : null;
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget(self::CACHE_KEY));
        static::deleted(fn () => Cache::forget(self::CACHE_KEY));
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
