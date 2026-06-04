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

/**
 * Offre d'emploi publiée par une entreprise.
 */
#[Fillable([
    'company_id',
    'category_id',
    'title',
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

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type'     => JobOfferType::class,
            'status'   => JobOfferStatus::class,
            'deadline' => 'date',
        ];
    }

    /**
     * Offres actives non expirées (deadline nulle ou future).
     *
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->where('status', JobOfferStatus::ACTIVE)
            ->where(function (Builder $q) {
                $q->whereNull('deadline')
                    ->orWhere('deadline', '>=', now()->toDateString());
            });
    }

    /**
     * Entreprise qui publie l'offre.
     *
     * @return BelongsTo<Company, $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Catégorie métier de l'offre.
     *
     * @return BelongsTo<JobCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(JobCategory::class, 'category_id');
    }

    /**
     * Candidatures reçues.
     *
     * @return HasMany<JobApplication, $this>
     */
    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    /**
     * Favoris enregistrés par les membres (Sprint 2).
     *
     * @return HasMany<JobFavorite, $this>
     */
    public function favorites(): HasMany
    {
        return $this->hasMany(JobFavorite::class);
    }

    /**
     * Compétences requises (relation N–N).
     *
     * @return BelongsToMany<JobSkill, $this>
     */
    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(
            JobSkill::class,
            'job_skill_pivot',
            'job_offer_id',
            'job_skill_id',
        )->withTimestamps();
    }
}
