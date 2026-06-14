<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable([
    'created_by', 'title', 'slug', 'description', 'program', 'type',
    'location', 'online_url', 'cover_image', 'starts_at', 'ends_at',
    'capacity', 'registrations_count', 'waitlist_enabled', 'status',
    'cancellation_reason', 'reminder_7d_sent', 'reminder_1d_sent',
    'is_paid', 'price', 'currency',
    'promo_code', 'promo_discount_type', 'promo_discount_value',
    'promo_expires_at', 'promo_max_uses', 'promo_uses_count',
    'ticketing_enabled', 'ticket_prefix', 'guest_registration_enabled',
    'recap_summary', 'recap_content', 'recap_video_url_1', 'recap_video_url_2',
    'recap_video_url_3', 'recap_document_path', 'recap_document_name',
    'recap_published_at', 'recap_published_by',
])]
class Event extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'status', 'cancellation_reason'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('event');
    }

    protected function casts(): array
    {
        return [
            'starts_at'                  => 'datetime',
            'ends_at'                    => 'datetime',
            'promo_expires_at'           => 'datetime',
            'waitlist_enabled'           => 'boolean',
            'is_paid'                    => 'boolean',
            'reminder_7d_sent'           => 'boolean',
            'reminder_1d_sent'           => 'boolean',
            'capacity'                   => 'integer',
            'registrations_count'        => 'integer',
            'price'                      => 'decimal:2',
            'promo_discount_value'       => 'decimal:2',
            'promo_max_uses'             => 'integer',
            'promo_uses_count'           => 'integer',
            'ticketing_enabled'          => 'boolean',
            'guest_registration_enabled' => 'boolean',
            'recap_published_at'         => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function confirmedRegistrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class)->where('status', 'confirmed');
    }

    public function guestRegistrations(): HasMany
    {
        return $this->hasMany(GuestRegistration::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(EventPhoto::class)->orderBy('order');
    }

    public function recapPublisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recap_published_by');
    }

    public function confirmedGuestRegistrations(): HasMany
    {
        return $this->hasMany(GuestRegistration::class)->where('status', 'confirmed');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('starts_at', '>', now());
    }

    public function scopePast(Builder $query): Builder
    {
        return $query->where('starts_at', '<=', now());
    }

    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function isFull(): bool
    {
        return $this->capacity !== null && $this->registrations_count >= $this->capacity;
    }

    public function isUpcoming(): bool
    {
        return $this->starts_at->isFuture();
    }

    public function isPast(): bool
    {
        return $this->ends_at->isPast();
    }

    public function isOnline(): bool
    {
        return $this->online_url !== null;
    }

    public function isFree(): bool
    {
        return ! $this->is_paid;
    }

    public function spotsLeft(): ?int
    {
        return $this->capacity ? $this->capacity - $this->registrations_count : null;
    }

    /**
     * Validates a promo code and returns the discount amount.
     * Returns null if the code is invalid or expired.
     *
     * @return array{discount: float, type: string}|null
     */
    public function resolvePromo(string $code): ?array
    {
        if (! $this->is_paid || $this->promo_code === null) {
            return null;
        }

        if (! hash_equals(strtoupper($this->promo_code), strtoupper($code))) {
            return null;
        }

        if ($this->promo_expires_at !== null && $this->promo_expires_at->isPast()) {
            return null;
        }

        if ($this->promo_max_uses !== null && $this->promo_uses_count >= $this->promo_max_uses) {
            return null;
        }

        return [
            'discount' => (float) $this->promo_discount_value,
            'type'     => $this->promo_discount_type,
        ];
    }

    /**
     * Computes the final price after applying an optional promo code.
     */
    public function computePrice(?string $promoCode = null): float
    {
        if (! $this->is_paid || $this->price === null) {
            return 0.0;
        }

        $base = (float) $this->price;

        if ($promoCode !== null) {
            $promo = $this->resolvePromo($promoCode);

            if ($promo !== null) {
                $discount = $promo['type'] === 'percent'
                    ? $base * ($promo['discount'] / 100)
                    : $promo['discount'];

                return max(0.0, round($base - $discount, 2));
            }
        }

        return $base;
    }

    /**
     * Vérifie si l'événement dispose d'un récapitulatif publié.
     */
    public function hasRecap(): bool
    {
        return $this->recap_published_at !== null;
    }

    /**
     * Retourne les URLs vidéo du récapitulatif sous forme de tableau (sans valeurs vides).
     *
     * @return array<int, string>
     */
    public function recapVideoUrls(): array
    {
        return array_values(array_filter([
            $this->recap_video_url_1,
            $this->recap_video_url_2,
            $this->recap_video_url_3,
        ]));
    }

    /**
     * Retourne l'URL publique du document de récapitulatif, ou null.
     */
    public function recapDocumentUrl(): ?string
    {
        if ($this->recap_document_path === null) {
            return null;
        }

        return asset('assets/documents/events/' . $this->recap_document_path);
    }

    /**
     * Convertit une URL YouTube/Vimeo en URL d'intégration (embed).
     */
    public function toEmbedUrl(string $url): string
    {
        if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $matches)
            || preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }

        if (preg_match('/vimeo\.com\/(\d+)/', $url, $matches)) {
            return 'https://player.vimeo.com/video/' . $matches[1];
        }

        return $url;
    }
}
