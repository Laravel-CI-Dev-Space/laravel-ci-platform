<?php

namespace App\Models;

use Database\Factories\JobSkillFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name', 'slug'])]
class JobSkill extends Model
{
    /** @use HasFactory<JobSkillFactory> */
    use HasFactory;

    public $timestamps = false;

    public function jobOffers(): BelongsToMany
    {
        return $this->belongsToMany(
            JobOffer::class,
            'job_skill_pivot',
            'job_skill_id',
            'job_offer_id',
        );
    }
}
