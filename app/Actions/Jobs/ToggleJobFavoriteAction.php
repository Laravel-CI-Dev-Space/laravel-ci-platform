<?php

declare(strict_types=1);

namespace App\Actions\Jobs;

use App\Models\JobFavorite;
use App\Models\JobOffer;
use App\Models\User;

final class ToggleJobFavoriteAction
{
    /**
     * @return bool True when favorited after toggle, false when removed.
     */
    public function execute(User $user, JobOffer $jobOffer): bool
    {
        $favorite = JobFavorite::query()
            ->where('user_id', $user->id)
            ->where('job_offer_id', $jobOffer->id)
            ->first();

        if ($favorite !== null) {
            $favorite->delete();

            return false;
        }

        JobFavorite::create([
            'user_id'      => $user->id,
            'job_offer_id' => $jobOffer->id,
        ]);

        return true;
    }
}
