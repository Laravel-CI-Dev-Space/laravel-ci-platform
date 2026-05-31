<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Question;
use App\Models\User;

class QuestionPolicy
{
    public function create(User $user): bool
    {
        return $user->isActive()
            && $user->hasAnyRole(['membre-actif', 'moderateur', 'admin', 'super-admin']);
    }

    public function update(User $user, Question $question): bool
    {
        return $user->id === $question->user_id
            || $user->hasAnyRole(['moderateur', 'admin', 'super-admin']);
    }

    public function delete(User $user, Question $question): bool
    {
        return $user->id === $question->user_id
            || $user->hasAnyRole(['moderateur', 'admin', 'super-admin']);
    }

    public function pin(User $user): bool
    {
        return $user->hasAnyRole(['moderateur', 'admin', 'super-admin']);
    }

    public function close(User $user): bool
    {
        return $user->hasAnyRole(['moderateur', 'admin', 'super-admin']);
    }
}
