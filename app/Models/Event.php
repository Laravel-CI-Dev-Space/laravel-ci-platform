<?php

namespace App\Models;

use App\Enums\Events\EventStatus;
use Database\Factories\EventFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable([
    'title',
    'slug',
    'description',
    'cover',
    'type_id',
    'location',
    'meeting_link',
    'start_date',
    'end_date',
    'capacity',
    'status',
])]
class Event extends Model
{
    /** @use HasFactory<EventFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date'   => 'datetime',
            'capacity'   => 'integer',
            'status'     => EventStatus::class,
        ];
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(EventType::class, 'type_id');
    }

    public function speakers(): HasMany
    {
        return $this->hasMany(EventSpeaker::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function waitlists(): HasMany
    {
        return $this->hasMany(EventWaitlist::class);
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(EventReminder::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(EventMedia::class);
    }

    public function icsExports(): HasMany
    {
        return $this->hasMany(EventIcsExport::class);
    }

    /** @param Builder<self> $query */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', EventStatus::PUBLISHED);
    }

    /** @param Builder<self> $query */
    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->published()->where('start_date', '>=', now());
    }

    /** @param Builder<self> $query */
    public function scopePast(Builder $query): Builder
    {
        return $query->published()->where('end_date', '<', now());
    }

    /** @param Builder<self> $query */
    public function scopeOfType(Builder $query, string $typeSlug): Builder
    {
        return $query->whereHas('type', fn (Builder $q) => $q->where('slug', $typeSlug));
    }

    protected static function booted(): void
    {
        static::creating(function (Event $event) {
            if (empty($event->slug)) {
                $event->slug = static::uniqueSlug($event->title);
            }
        });
    }

    protected static function uniqueSlug(string $title): string
    {
        $slug     = Str::slug($title);
        $original = $slug;
        $count    = 1;

        while (static::query()->where('slug', $slug)->exists()) {
            $slug = $original . '-' . $count;
            $count++;
        }

        return $slug;
    }
}
