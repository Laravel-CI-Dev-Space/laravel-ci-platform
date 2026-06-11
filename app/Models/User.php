<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

#[Fillable([
    'name',
    'email',
    'avatar',
    'github_id',
    'github_username',
    'is_active',
    'suspended_until',
    'last_login_at',
    'email_verified_at',
])]
#[Hidden(['remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, LogsActivity, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at'     => 'datetime',
            'suspended_until'   => 'datetime',
            'is_active'         => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['is_active', 'suspended_until'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('user');
    }

    // ─── SCOPES ──────────────────────────────────────────

    /** Users who are fully active (not banned, not suspended). */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('suspended_until')
                    ->orWhere('suspended_until', '<', now());
            });
    }

    /** Users who are permanently banned (is_active = false). */
    public function scopeBanned($query)
    {
        return $query->where('is_active', false);
    }

    /** Users who are temporarily suspended (suspended_until in the future). */
    public function scopeSuspended($query)
    {
        return $query->where('is_active', true)
            ->whereNotNull('suspended_until')
            ->where('suspended_until', '>', now());
    }

    // ─── HELPERS ─────────────────────────────────────────

    /** Returns true if the account is permanently banned (is_active = false). */
    public function isBanned(): bool
    {
        return ! $this->is_active;
    }

    /** Returns true if the account is temporarily suspended. */
    public function isSuspended(): bool
    {
        return $this->is_active
            && $this->suspended_until !== null
            && $this->suspended_until->isFuture();
    }

    /** Returns true if the account is fully active (not banned, not suspended). */
    public function isActive(): bool
    {
        return $this->is_active && ! $this->isSuspended();
    }

    /** Returns the number of days remaining in a temporary suspension. */
    public function suspensionDaysLeft(): int
    {
        if (! $this->isSuspended()) {
            return 0;
        }

        return (int) now()->diffInDays($this->suspended_until);
    }

    /** Returns the public GitHub profile URL for this user. */
    public function githubUrl(): string
    {
        return "https://github.com/{$this->github_username}";
    }

    // ─── RELATIONS ───────────────────────────────────────

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function eventRegistrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function jobApplications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    public function jobFavorites(): HasMany
    {
        return $this->hasMany(JobFavorite::class);
    }

    /** Reports filed by this user. */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }

    /** Returns true if the user has created their profile at least once. */
    public function hasCompletedProfile(): bool
    {
        return $this->profile !== null;
    }
}
