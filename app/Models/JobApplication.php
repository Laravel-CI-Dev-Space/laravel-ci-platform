<?php

// JobApplication

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'job_offer_id', 'user_id', 'cv_path', 'cover_letter',
    'portfolio_url', 'linkedin_url', 'status', 'employer_note', 'viewed_at',
])]
class JobApplication extends Model
{
    protected function casts(): array
    {
        return ['viewed_at' => 'datetime'];
    }

    public function jobOffer(): BelongsTo
    {
        return $this->belongsTo(JobOffer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
