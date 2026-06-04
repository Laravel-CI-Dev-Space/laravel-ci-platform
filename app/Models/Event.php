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

/**
 * Événement de la communauté (meetup, webinar, hackathon).
 */
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

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date'   => 'datetime',
            'capacity'   => 'integer',
            'status'     => EventStatus::class,
        ];
    }

    /**
     * Catégorie de l'événement.
     *
     * @return BelongsTo<EventType, $this>
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(EventType::class, 'type_id');
    }

    /**
     * Intervenants de l'événement.
     *
     * @return HasMany<EventSpeaker, $this>
     */
    public function speakers(): HasMany
    {
        return $this->hasMany(EventSpeaker::class);
    }

    /**
     * Inscriptions confirmées ou en attente.
     *
     * @return HasMany<EventRegistration, $this>
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    /**
     * Membres en liste d'attente.
     *
     * @return HasMany<EventWaitlist, $this>
     */
    public function waitlists(): HasMany
    {
        return $this->hasMany(EventWaitlist::class);
    }

    /**
     * Rappels planifiés (Sprint 2).
     *
     * @return HasMany<EventReminder, $this>
     */
    public function reminders(): HasMany
    {
        return $this->hasMany(EventReminder::class);
    }

    /**
     * Médias attachés (Sprint 2).
     *
     * @return HasMany<EventMedia, $this>
     */
    public function media(): HasMany
    {
        return $this->hasMany(EventMedia::class);
    }

    /**
     * Exports iCal générés (Sprint 2).
     *
     * @return HasMany<EventIcsExport, $this>
     */
    public function icsExports(): HasMany
    {
        return $this->hasMany(EventIcsExport::class);
    }

    /**
     * Événements au statut publié.
     *
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', EventStatus::PUBLISHED);
    }

    /**
     * Événements publiés dont la date de début est future.
     *
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->published()->where('start_date', '>=', now());
    }

    /**
     * Événements publiés déjà terminés.
     *
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopePast(Builder $query): Builder
    {
        return $query->published()->where('end_date', '<', now());
    }

    /**
     * Filtre par slug du type d'événement.
     *
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeOfType(Builder $query, string $typeSlug): Builder
    {
        return $query->whereHas('type', fn (Builder $q) => $q->where('slug', $typeSlug));
    }

    /**
     * Génère un slug unique à la création si absent.
     */
    protected static function booted(): void
    {
        static::creating(function (Event $event) {
            if (empty($event->slug)) {
                $event->slug = static::uniqueSlug($event->title);
            }
        });
    }

    /**
     * Slug unique dérivé du titre.
     */
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
