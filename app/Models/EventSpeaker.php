<?php

namespace App\Models;

use Database\Factories\EventSpeakerFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'event_id',
    'name',
    'bio',
    'photo',
    'linkedin',
    'github',
])]
class EventSpeaker extends Model
{
    /** @use HasFactory<EventSpeakerFactory> */
    use HasFactory;

    public $timestamps = false;

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
