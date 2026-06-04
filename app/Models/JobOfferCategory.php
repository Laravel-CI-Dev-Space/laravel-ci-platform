<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Catégorie d'offre (legacy — préférer {@see JobCategory} + table job_categories).
 */
#[Fillable(['name', 'slug', 'icon', 'offers_count'])]
class JobOfferCategory extends Model
{
    /**
     * Offres liées via la table pivot legacy.
     *
     * @return BelongsToMany<JobOffer, $this>
     */
    public function jobOffers(): BelongsToMany
    {
        return $this->belongsToMany(JobOffer::class, 'job_offer_category');
    }
}
