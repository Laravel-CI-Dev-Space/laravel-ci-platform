<?php

namespace App\Models;

use Database\Factories\JobCategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Catégorie métier d'une offre (backend, frontend, DevOps…).
 */
#[Fillable(['name', 'slug'])]
class JobCategory extends Model
{
    /** @use HasFactory<JobCategoryFactory> */
    use HasFactory;

    /**
     * Offres rattachées à cette catégorie.
     *
     * @return HasMany<JobOffer, $this>
     */
    public function jobOffers(): HasMany
    {
        return $this->hasMany(JobOffer::class, 'category_id');
    }
}
