<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'first_name', 'last_name', 'role', 'avatar', 'avatar_initials',
    'avatar_color', 'github_url', 'linkedin_url', 'twitter_url', 'bio', 'order', 'is_active',
])]
class TeamMember extends Model
{
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /** Scope : membres actifs triés par ordre. */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('order');
    }

    /** Retourne le nom complet du membre. */
    public function fullName(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /** Retourne l'URL publique de l'avatar ou null si absent. */
    public function avatarUrl(): ?string
    {
        if (! $this->avatar) {
            return null;
        }

        return asset('assets/' . $this->avatar);
    }

    /** Retourne les initiales du membre (depuis avatar_initials ou prénom+nom). */
    public function initials(): string
    {
        if ($this->avatar_initials) {
            return strtoupper($this->avatar_initials);
        }

        $first = mb_substr($this->first_name, 0, 1);
        $last  = mb_substr($this->last_name, 0, 1);

        return strtoupper($first . $last);
    }
}
