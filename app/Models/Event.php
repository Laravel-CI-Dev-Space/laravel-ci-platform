<?php

namespace App\Models;

use App\Enums\Events\EventRegistrationStatus;
use App\Enums\Events\EventStatus;
use Database\Factories\EventFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
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

    /**
     * URL absolue de la cover.
     *
     * Formats supportés :
     * - URL externe (seeders, picsum…) : https://…
     * - Upload Filament (disque public) : events/cover.jpg → /storage/events/…
     * - Asset statique public : assets/web/img/…
     */
    public function coverUrl(): ?string
    {
        $path = $this->normalizeCoverPath();

        if ($path === null) {
            return null;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        $path = ltrim($path, '/');

        if (str_starts_with($path, 'storage/')) {
            return url($path);
        }

        if (str_starts_with($path, 'assets/')) {
            return asset($path);
        }

        return Storage::disk('public')->url($path);
    }

    private function normalizeCoverPath(): ?string
    {
        $cover = $this->cover;

        if (blank($cover)) {
            return null;
        }

        if (is_array($cover)) {
            $cover = $cover[array_key_first($cover)] ?? null;
        }

        if (! is_string($cover)) {
            return null;
        }

        $cover = trim($cover);

        return $cover !== '' ? $cover : null;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKey(): mixed
    {
        return $this->resolveSlug();
    }

    /**
     * Garantit un slug utilisable pour les routes (génère et persiste si absent).
     */
    public function resolveSlug(): string
    {
        if (! empty($this->slug)) {
            return $this->slug;
        }

        $slug = static::uniqueSlug($this->title);
        $this->forceFill(['slug' => $slug])->saveQuietly();

        return $slug;
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

    public function confirmedRegistrationsCount(): int
    {
        return $this->registrations()
            ->where('status', EventRegistrationStatus::CONFIRMED)
            ->count();
    }

    public function isFull(): bool
    {
        if ($this->capacity === null) {
            return false;
        }

        $count = $this->confirmed_registrations_count ?? null;

        if ($count !== null) {
            return (int) $count >= $this->capacity;
        }

        return $this->confirmedRegistrationsCount() >= $this->capacity;
    }

    public function isUpcoming(): bool
    {
        return $this->start_date->isFuture();
    }

    /** Past events use end_date; upcoming checks start_date only. */
    public function isPast(): bool
    {
        return $this->end_date->isPast();
    }

    public function isRegisterable(): bool
    {
        return $this->status === EventStatus::PUBLISHED
            && $this->isUpcoming();
    }

    /**
     * Active registration for a user (excludes cancelled rows).
     *
     * Cancelled registrations stay in DB because of unique (event_id, user_id).
     */
    public function registrationFor(?User $user): ?EventRegistration
    {
        if ($user === null) {
            return null;
        }

        if ($this->relationLoaded('registrations')) {
            return $this->registrations
                ->first(fn (EventRegistration $registration) => $registration->user_id === $user->id
                    && $registration->status !== EventRegistrationStatus::CANCELLED);
        }

        return $this->registrations()
            ->where('user_id', $user->id)
            ->whereNot('status', EventRegistrationStatus::CANCELLED)
            ->first();
    }

    public function waitlistEntryFor(?User $user): ?EventWaitlist
    {
        if ($user === null) {
            return null;
        }

        if ($this->relationLoaded('waitlists')) {
            return $this->waitlists->first();
        }

        return $this->waitlists()->where('user_id', $user->id)->first();
    }

    /**
     * Build props for the public <x-web.event-card> component.
     *
     * @return array<string, mixed>
     */
    public function toWebCardProps(): array
    {
        $taken    = (int) ($this->confirmed_registrations_count ?? $this->confirmedRegistrationsCount());
        $total    = (int) ($this->capacity ?? 0);
        $typeSlug = $this->type?->slug ?? 'meetup';

        return [
            'type'         => $typeSlug,
            'typeLabel'    => $this->type?->name ?? 'Événement',
            'cover'        => $this->coverUrl(),
            'title'        => $this->title,
            'month'        => $this->start_date->translatedFormat('M'),
            'day'          => $this->start_date->format('d'),
            'time'         => $this->start_date->format('D H:i'),
            'location'     => $this->location ?? $this->meeting_link ?? 'En ligne',
            'spotsUsed'    => $taken,
            'spotsTotal'   => $total,
            'href'         => route('events.show', $this),
            'registerHref' => route('events.show', $this),
            'past'         => $this->end_date->isPast(),
        ];
    }

    /**
     * Données pour le composant <x-card.event>.
     *
     * @return array<string, mixed>
     */
    public function toCardData(): array
    {
        $taken = (int) ($this->confirmed_registrations_count ?? $this->confirmedRegistrationsCount());
        $total = $this->capacity ?? 0;

        return [
            'title'       => $this->title,
            'location'    => $this->location ?? $this->meeting_link ?? 'En ligne',
            'time'        => $this->start_date->format('d/m/Y H:i') . ' — ' . $this->end_date->format('H:i'),
            'description' => Str::limit(strip_tags($this->description), 120),
            'image'       => $this->coverUrl(),
            'seats_taken' => $taken,
            'seats_total' => max($total, 1),
            'date_day'    => $this->start_date->format('d'),
            'date_month'  => $this->start_date->translatedFormat('M'),
            'cta_label'   => $this->isFull() ? 'Liste d\'attente' : 'Voir & s\'inscrire',
            'cta_url'     => route('events.show', ['event' => $this->resolveSlug()]),
            'type_label'  => $this->type?->name,
        ];
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
