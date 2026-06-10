<?php

namespace App\Models;

use App\Enums\Events\EventMediaType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'event_id',
    'type',
    'url',
])]
class EventMedia extends Model
{
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
}
