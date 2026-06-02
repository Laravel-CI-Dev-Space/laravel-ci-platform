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
}
