<?php

namespace App\Policies;

use App\Models\JobOffer;
use App\Models\User;

class JobOfferPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, JobOffer $jobOffer): bool
    {
        if ($jobOffer->isPubliclyVisible()) {
            return true;
        }

        return $user !== null && $user->hasAnyRole(['super-admin', 'admin', 'moderator']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('member');
    }

    public function apply(User $user, JobOffer $jobOffer): bool
    {
        if (! $user->hasRole('member')) {
            return false;
        }

        if (! $jobOffer->isApplyable()) {
            return false;
        }

        return $jobOffer->applicationFor($user) === null;
    }
}
