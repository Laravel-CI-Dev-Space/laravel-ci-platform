<?php

namespace App\Models;

use App\Models\Concerns\CachesActiveRecords;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

#[Fillable(['icon', 'label', 'value', 'suffix', 'auto_count', 'model', 'order', 'is_active'])]
class HomeStat extends Model
{
    use CachesActiveRecords;

    protected function casts(): array
    {
        return [
            'auto_count' => 'boolean',
            'is_active'  => 'boolean',
            'value'      => 'integer',
        ];
    }

    /** Scope : statistiques actives triées par ordre. */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('order');
    }

    /**
     * Retourne la valeur résolue (auto-comptage ou valeur manuelle).
     * Le comptage automatique est mis en cache 10 minutes pour éviter
     * un COUNT(*) répété sur de grosses tables à chaque affichage de
     * la page d'accueil.
     */
    public function resolvedValue(): int
    {
        if ($this->auto_count && $this->model && class_exists($this->model)) {
            return Cache::remember(
                "home_stat:{$this->id}:auto_count",
                now()->addMinutes(10),
                fn () => (int) app($this->model)::count()
            );
        }

        return $this->value;
    }
}
