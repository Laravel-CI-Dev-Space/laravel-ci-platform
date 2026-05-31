<?php

namespace App\Models;

use App\Models\Company;
use App\Models\JobApplication;
use App\Models\JobFavorite;
use App\Models\JobOfferCategory;
use App\Models\JobSkill;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'company_id', 'posted_by', 'title', 'slug', 'description',
    'contract_type', 'level', 'location', 'country', 'is_remote',
    'is_hybrid', 'salary_min', 'salary_max', 'currency', 'salary_visible',
    'status', 'rejection_reason', 'is_urgent', 'views_count',
    'applications_count', 'expires_at', 'published_at',
])]
class JobOffer extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'is_remote'          => 'boolean',
            'is_hybrid'          => 'boolean',
            'is_urgent'          => 'boolean',
            'salary_visible'     => 'boolean',
            'expires_at'         => 'datetime',
            'published_at'       => 'datetime',
            'views_count'        => 'integer',
            'applications_count' => 'integer',
            'salary_min'         => 'integer',
            'salary_max'         => 'integer',
        ];
    }

    public function company(): BelongsTo        { return $this->belongsTo(Company::class); }
    public function poster(): BelongsTo         { return $this->belongsTo(User::class, 'posted_by'); }
    public function categories(): BelongsToMany { return $this->belongsToMany(JobOfferCategory::class, 'job_offer_category'); }
    public function skills(): BelongsToMany     { return $this->belongsToMany(JobSkill::class, 'job_offer_skill'); }
    public function applications(): HasMany      { return $this->hasMany(JobApplication::class); }
    public function favorites(): HasMany         { return $this->hasMany(JobFavorite::class); }
    public function reports(): MorphMany         { return $this->morphMany(Report::class, 'reportable'); }

    public function scopeActive($query)   { return $query->where('status', 'active'); }
    public function scopeRemote($query)   { return $query->where('is_remote', true); }
    public function scopeUrgent($query)   { return $query->where('is_urgent', true); }
    public function scopeNotExpired($query) { return $query->where(function ($q) {
        $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
    }); }

    public function isExpired(): bool     { return $this->expires_at && $this->expires_at->isPast(); }
    public function isNew(): bool         { return $this->published_at && $this->published_at->diffInDays(now()) <= 7; }
    public function salaryRange(): ?string
    {
        if (! $this->salary_visible || ! $this->salary_min) return null;
        if ($this->salary_max) return number_format($this->salary_min) . ' - ' . number_format($this->salary_max) . ' ' . $this->currency;
        return 'À partir de ' . number_format($this->salary_min) . ' ' . $this->currency;
    }
}
