<?php

namespace App\Models;

use Database\Factories\EventTypeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug'])]
class EventType extends Model
{
    /** @use HasFactory<EventTypeFactory> */
    use HasFactory;

    public $timestamps = false;

    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'type_id');
    }
}
