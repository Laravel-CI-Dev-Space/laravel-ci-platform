<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name', 'slug', 'min_points', 'color', 'icon', 'description', 'order',
])]
class Grade extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'min_points' => 'integer',
            'order'      => 'integer',
        ];
    }

    public function profiles(): HasMany
    {
        return $this->hasMany(Profile::class);
    }

    /**
     * Récupère le grade le plus élevé (utilisé pour le super-admin).
     */
    public static function highest(): ?self
    {
        return static::orderByDesc('order')->first();
    }

    /**
     * Récupère le grade correspondant à un total de points donné.
     */
    public static function forPoints(int $points): ?self
    {
        return static::where('min_points', '<=', $points)
            ->orderByDesc('min_points')
            ->first();
    }
}
