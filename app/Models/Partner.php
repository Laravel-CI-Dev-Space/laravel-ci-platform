<?php

namespace App\Models;

use App\Models\Concerns\CachesActiveRecords;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'logo', 'icon', 'url', 'type', 'order', 'is_active'])]
class Partner extends Model
{
    use CachesActiveRecords;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /** Scope : partenaires actifs triés par ordre. */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('order');
    }

    /** Retourne l'URL publique du logo ou null si absent. */
    public function logoUrl(): ?string
    {
        if (! $this->logo) {
            return null;
        }

        return asset('assets/' . $this->logo);
    }
}
