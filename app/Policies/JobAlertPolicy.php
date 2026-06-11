<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\JobAlert;
use App\Models\User;

class JobAlertPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('member');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('member');
    }

    public function update(User $user, JobAlert $jobAlert): bool
    {
        return $user->id === $jobAlert->user_id;
    }

    public function delete(User $user, JobAlert $jobAlert): bool
    {
        return $user->id === $jobAlert->user_id;
    }
}
