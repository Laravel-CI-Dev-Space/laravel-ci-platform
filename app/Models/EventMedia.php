<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\EventMediaType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'event_id', 'type', 'disk', 'path', 'original_name',
    'mime_type', 'file_size', 'thumbnail_path', 'caption', 'order',
])]
class EventMedia extends Model
{
    protected function casts(): array
    {
        return [
            'type'      => EventMediaType::class,
            'file_size' => 'integer',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    // ── URLs publiques ─────────────────────────────────────────────────────

    /**
     * URL publique du média original (depuis R2 via CDN Cloudflare).
     */
    public function url(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    /**
     * URL du thumbnail (photos uniquement).
     * Fallback sur l'original si pas de thumbnail.
     */
    public function thumbnailUrl(): string
    {
        if ($this->thumbnail_path) {
            return Storage::disk($this->disk)->url($this->thumbnail_path);
        }

        return $this->url();
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    public function isPhoto(): bool
    {
        return $this->type === EventMediaType::Photo;
    }

    public function isVideo(): bool
    {
        return $this->type === EventMediaType::Video;
    }

    /**
     * Taille lisible : "2.4 Mo", "14.5 Ko".
     */
    public function humanSize(): string
    {
        if ($this->file_size === null) {
            return '—';
        }

        $bytes = $this->file_size;

        if ($bytes >= 1_048_576) {
            return round($bytes / 1_048_576, 1) . ' Mo';
        }

        return round($bytes / 1024, 1) . ' Ko';
    }

    /**
     * Extension depuis le nom original.
     */
    public function extension(): string
    {
        return strtolower(pathinfo($this->original_name, PATHINFO_EXTENSION));
    }
}
