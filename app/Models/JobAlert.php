<?php

namespace App\Models;

use App\Enums\Jobs\JobOfferType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'keywords',
    'location',
    'type',
    'is_active',
])]
class JobAlert extends Model
{
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'type'      => JobOfferType::class,
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
