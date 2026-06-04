<?php

namespace App\Models;

use Database\Factories\CompanyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Entreprise associée à une ou plusieurs offres d'emploi.
 */
#[Fillable([
    'name',
    'description',
    'logo',
    'website',
])]
class Company extends Model
{
    /** @use HasFactory<CompanyFactory> */
    use HasFactory;

    /**
     * Les offres d'emploi de cette compagnie
     *
     * @return HasMany<JobOffer, $this>
     */
    public function jobOffers(): HasMany
    {
        return $this->hasMany(JobOffer::class);
    }
}
