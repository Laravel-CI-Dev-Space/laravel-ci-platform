<?php

namespace App\Models;

use App\Models\Concerns\CachesActiveRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pole extends Model
{
    use CachesActiveRecords;
    use HasFactory;

    protected $fillable = ['name', 'slug', 'position', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('position');
    }

    public function members(): HasMany
    {
        return $this->hasMany(PoleMember::class)->orderBy('order');
    }

    public function activeMembers(): HasMany
    {
        return $this->hasMany(PoleMember::class)
            ->where('status', 'actif')
            ->orderBy('order');
    }
}
