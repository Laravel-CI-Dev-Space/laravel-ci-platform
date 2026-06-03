<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Question;
use App\Models\User;

class QuestionPolicy
{
    /**
     * Determine whether the user can view any questions.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the question.
     */
    public function view(User $user, Question $question): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create questions.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('forum.question.create');
    }

    /**
     * Determine whether the user can update the question.
     */
    public function update(User $user, Question $question): bool
    {
        return $user->id === $question->user_id || $user->hasPermissionTo('forum.question.edit');
    }

    /**
     * Determine whether the user can delete the question.
     */
    public function delete(User $user, Question $question): bool
    {
        return $user->id === $question->user_id || $user->hasPermissionTo('forum.question.delete');
    }

    /**
     * Determine whether the user can restore the question.
     */
    public function restore(User $user, Question $question): bool
    {
        return $user->hasPermissionTo('forum.question.delete');
    }

    /**
     * Determine whether the user can permanently delete the question.
     */
    public function forceDelete(User $user, Question $question): bool
    {
        return $user->hasPermissionTo('forum.question.delete');
    }

    /**
     * Determine whether the user can pin the question.
     */
    public function pin(User $user, Question $question): bool
    {
        return $user->hasPermissionTo('forum.question.pin');
    }
}
