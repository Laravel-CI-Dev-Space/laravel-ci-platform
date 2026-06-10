<?php

namespace App\Models;

use App\Enums\Events\EventReminderType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'event_id',
    'type',
    'scheduled_at',
    'sent_at',
])]
class EventReminder extends Model
{
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'type'         => EventReminderType::class,
            'scheduled_at' => 'datetime',
            'sent_at'      => 'datetime',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
