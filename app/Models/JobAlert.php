<?php

namespace App\Models;

use App\Enums\Jobs\JobOfferType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Alerte emploi personnalisée d'un membre (Sprint 2).
 */
#[Fillable([
    'user_id',
    'keywords',
    'location',
    'type',
    'is_active',
])]
class JobAlert extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type'      => JobOfferType::class,
            'is_active' => 'boolean',
        ];
    }

    /**
     * Membre propriétaire de l'alerte.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
