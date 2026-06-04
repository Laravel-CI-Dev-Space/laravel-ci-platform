<?php

namespace App\Models;

use App\Enums\Events\EventReminderType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Rappel planifié avant un événement (Sprint 2).
 */
#[Fillable([
    'event_id',
    'type',
    'scheduled_at',
    'sent_at',
])]
class EventReminder extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type'         => EventReminderType::class,
            'scheduled_at' => 'datetime',
            'sent_at'      => 'datetime',
        ];
    }

    /**
     * Événement concerné par le rappel.
     *
     * @return BelongsTo<Event, $this>
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
