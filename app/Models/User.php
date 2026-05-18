<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
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
    use HasFactory, Notifiable, HasRoles;

    /**
     * Cast des attributs.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at'     => 'datetime',
            'suspended_until'   => 'datetime',
            'is_active'         => 'boolean',
        ];
    }

    // ─── SCOPES ──────────────────────────────────────────

    /**
     * Membres actifs et non suspendus.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->where(function ($q) {
                         $q->whereNull('suspended_until')
                           ->orWhere('suspended_until', '<', now());
                     });
    }

    /**
     * Membres bannis définitivement.
     */
    public function scopeBanned($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Membres suspendus temporairement.
     */
    public function scopeSuspended($query)
    {
        return $query->where('is_active', true)
                     ->whereNotNull('suspended_until')
                     ->where('suspended_until', '>', now());
    }

    // ─── HELPERS ─────────────────────────────────────────

    /**
     * Vérifie si le membre est banni définitivement.
     */
    public function isBanned(): bool
    {
        return ! $this->is_active;
    }

    /**
     * Vérifie si le membre est suspendu temporairement.
     */
    public function isSuspended(): bool
    {
        return $this->is_active
            && $this->suspended_until !== null
            && $this->suspended_until->isFuture();
    }

    /**
     * Vérifie si le membre est pleinement actif.
     */
    public function isActive(): bool
    {
        return $this->is_active && ! $this->isSuspended();
    }

    /**
     * Retourne les jours restants de suspension.
     */
    public function suspensionDaysLeft(): int
    {
        if (! $this->isSuspended()) {
            return 0;
        }

        return (int) now()->diffInDays($this->suspended_until);
    }

    /**
     * Retourne l'URL du profil GitHub du membre.
     */
    public function githubUrl(): string
    {
        return "https://github.com/{$this->github_username}";
    }
}
