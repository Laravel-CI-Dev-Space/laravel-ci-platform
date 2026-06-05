<?php

// JobSkill

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name', 'slug', 'offers_count'])]
class JobSkill extends Model
{
    public function jobOffers(): BelongsToMany
    {
        return $this->belongsToMany(JobOffer::class, 'job_offer_skill');
    }
}
