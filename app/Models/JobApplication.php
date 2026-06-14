<?php

// JobApplication

namespace App\Models;

use App\Enums\JobApplicationStatus;
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
        return [
            'viewed_at' => 'datetime',
            'status'    => JobApplicationStatus::class,
        ];
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
        return $this->status === JobApplicationStatus::Pending;
    }

    public function isAccepted(): bool
    {
        return $this->status === JobApplicationStatus::Accepted;
    }

    public function isRejected(): bool
    {
        return $this->status === JobApplicationStatus::Rejected;
    }
}
