<?php

// JobOfferCategory

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name', 'slug', 'icon', 'offers_count'])]
class JobOfferCategory extends Model
{
    public function jobOffers(): BelongsToMany
    {
        return $this->belongsToMany(JobOffer::class, 'job_offer_category');
    }
}
