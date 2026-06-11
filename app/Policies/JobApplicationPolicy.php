<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\JobApplication;
use App\Models\User;

class JobApplicationPolicy
{
    public function view(User $user, JobApplication $jobApplication): bool
    {
        return $user->id === $jobApplication->user_id || $user->hasRole('super-admin') || $user->hasRole('admin') || $user->hasRole('moderator');
    }
}
