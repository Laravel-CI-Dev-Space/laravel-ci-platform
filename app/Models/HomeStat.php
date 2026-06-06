<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['icon', 'label', 'value', 'suffix', 'auto_count', 'model', 'order', 'is_active'])]
class HomeStat extends Model
{
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

    /** Retourne la valeur résolue (auto-comptage ou valeur manuelle). */
    public function resolvedValue(): int
    {
        if ($this->auto_count && $this->model && class_exists($this->model)) {
            return (int) app($this->model)::count();
        }

        return $this->value;
    }
}
