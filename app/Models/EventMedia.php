<?php

namespace App\Models;

use App\Enums\Events\EventMediaType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Média associé à un événement (image, vidéo, PDF).
 */
#[Fillable([
    'event_id',
    'type',
    'url',
])]
class EventMedia extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => EventMediaType::class,
        ];
    }

    /**
     * Événement propriétaire du média.
     *
     * @return BelongsTo<Event, $this>
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
