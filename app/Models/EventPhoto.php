<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['event_id', 'path', 'caption', 'order'])]
class EventPhoto extends Model
{
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * URL publique de la photo.
     */
    public function url(): string
    {
        return asset('assets/events/recap/' . $this->path);
    }
}
