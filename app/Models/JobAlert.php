<?php

namespace App\Models;

use App\Enums\Jobs\JobOfferType;
use Database\Factories\JobAlertFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
    /** @use HasFactory<JobAlertFactory> */
    use HasFactory;

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
