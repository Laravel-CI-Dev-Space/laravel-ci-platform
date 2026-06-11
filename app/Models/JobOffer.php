<?php

namespace App\Models;

use App\Enums\Jobs\JobOfferStatus;
use App\Enums\Jobs\JobOfferType;
use Database\Factories\JobOfferFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable([
    'company_id',
    'category_id',
    'title',
    'slug',
    'description',
    'location',
    'type',
    'salary',
    'deadline',
    'status',
])]
class JobOffer extends Model
{
    /** @use HasFactory<JobOfferFactory> */
    use HasFactory;

    public const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'type'       => JobOfferType::class,
            'status'     => JobOfferStatus::class,
            'deadline'   => 'date',
            'created_at' => 'datetime',
        ];
    }

    /** @param Builder<self> $query */
    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->where('status', JobOfferStatus::ACTIVE)
            ->where(function (Builder $q) {
                $q->whereNull('deadline')
                    ->orWhere('deadline', '>=', now()->toDateString());
            });
    }

    public function isPubliclyVisible(): bool
    {
        return $this->status === JobOfferStatus::ACTIVE && $this->isWithinDeadline();
    }

    public function isApplyable(): bool
    {
        return $this->isPubliclyVisible();
    }

    public function isWithinDeadline(): bool
    {
        return $this->deadline === null || ! $this->deadline->isPast();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

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

        $slug = static::uniqueSlug($this->title, $this->id);
        $this->forceFill(['slug' => $slug])->saveQuietly();

        return $slug;
    }

    public function applicationFor(?User $user): ?JobApplication
    {
        if ($user === null) {
            return null;
        }

        if ($this->relationLoaded('applications')) {
            return $this->applications->first();
        }

        return $this->applications()->where('user_id', $user->id)->first();
    }

    /**
     * Build props for the public <x-web.job-card> component.
     *
     * @return array<string, mixed>
     */
    public function toWebCardProps(): array
    {
        $this->loadMissing(['company', 'skills']);

        return [
            'logoClass'    => 'cl-' . ((($this->id ?? 1) % 6) + 1),
            'logoText'     => strtoupper(Str::substr($this->company->name, 0, 2)),
            'title'        => $this->title,
            'company'      => $this->company->name,
            'location'     => $this->location,
            'remote'       => $this->type === JobOfferType::REMOTE,
            'description'  => Str::limit(strip_tags($this->description), 140),
            'contractType' => $this->type->label(),
            'tags'         => $this->skills->pluck('name')->all(),
            'salary'       => $this->salary ?: '—',
            'href'         => route('jobs.show', $this),
            'badge'        => $this->created_at && $this->created_at->diffInDays(now()) <= 7 ? 'NEW' : null,
        ];
    }

    /**
     * Données pour le composant <x-card.job>.
     *
     * @return array<string, mixed>
     */
    public function toCardData(): array
    {
        $this->loadMissing(['company', 'skills']);

        return [
            'title'     => $this->title,
            'company'   => $this->company->name,
            'logo'      => $this->company->logo,
            'contract'  => $this->type->label(),
            'location'  => $this->location ?? '—',
            'remote'    => $this->type === JobOfferType::REMOTE,
            'stack'     => $this->skills->pluck('name')->all(),
            'salary'    => $this->salary,
            'posted_at' => $this->created_at?->diffForHumans() ?? '',
            'url'       => route('jobs.show', $this),
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(JobCategory::class, 'category_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(JobFavorite::class);
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(
            JobSkill::class,
            'job_skill_pivot',
            'job_offer_id',
            'job_skill_id',
        );
    }

    protected static function booted(): void
    {
        static::creating(function (JobOffer $offer) {
            if (empty($offer->slug)) {
                $offer->slug = static::uniqueSlug($offer->title);
            }
        });
    }

    public static function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $slug     = Str::slug($title);
        $original = $slug;
        $count    = 1;

        while (static::query()
            ->when($ignoreId !== null, fn (Builder $q) => $q->whereKeyNot($ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $original . '-' . $count;
            $count++;
        }

        return $slug;
    }
}
