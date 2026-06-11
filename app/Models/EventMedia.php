<?php

namespace App\Models;

use App\Enums\Events\EventMediaType;
use App\Support\MediaUrl;
use Database\Factories\EventMediaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'event_id',
    'type',
    'url',
])]
class EventMedia extends Model
{
    /** @use HasFactory<EventMediaFactory> */
    use HasFactory;

    public const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'type' => EventMediaType::class,
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function resolvedUrl(): ?string
    {
        return MediaUrl::resolve($this->url);
    }

    public function youtubeEmbedUrl(): ?string
    {
        if ($this->type !== EventMediaType::VIDEO) {
            return null;
        }

        $url = $this->resolvedUrl();

        if ($url === null) {
            return null;
        }

        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }

        return null;
    }
}
