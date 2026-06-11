<?php

declare(strict_types=1);

namespace App\Queries\Jobs;

use App\Models\JobFavorite;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

final class JobFavoriteListQuery
{
    /**
     * @return Builder<JobFavorite>
     */
    public static function forUser(User $user): Builder
    {
        return JobFavorite::query()
            ->where('user_id', $user->id)
            ->with(['jobOffer.company', 'jobOffer.skills'])
            ->latest();
    }
}
