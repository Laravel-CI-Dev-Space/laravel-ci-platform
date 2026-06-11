<?php

declare(strict_types=1);

namespace App\Services\Jobs;

use App\Actions\Jobs\ToggleJobFavoriteAction;
use App\Models\JobFavorite;
use App\Models\JobOffer;
use App\Models\User;

class JobFavoriteService
{
    public function __construct(
        private readonly ToggleJobFavoriteAction $toggleJobFavorite,
    ) {}

    /**
     * @return bool True when favorited after toggle, false when removed.
     */
    public function toggle(User $user, JobOffer $jobOffer): bool
    {
        return $this->toggleJobFavorite->execute($user, $jobOffer);
    }

    public function isFavorited(User $user, JobOffer $jobOffer): bool
    {
        return JobFavorite::query()
            ->where('user_id', $user->id)
            ->where('job_offer_id', $jobOffer->id)
            ->exists();
    }
}
