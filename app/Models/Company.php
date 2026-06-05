<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'submitted_by', 'name', 'slug', 'description', 'logo',
    'website', 'email', 'phone', 'country', 'city', 'is_verified',
])]
class Company extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return ['is_verified' => 'boolean'];
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function jobOffers(): HasMany
    {
        return $this->hasMany(JobOffer::class);
    }

    public function activeJobOffers(): HasMany
    {
        return $this->hasMany(JobOffer::class)->where('status', 'active');
    }

    public function logoUrl(): ?string
    {
        return $this->logo ? asset('assets/companies/' . $this->logo) : null;
    }
}
