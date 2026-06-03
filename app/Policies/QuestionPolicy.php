<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Question;
use App\Models\User;

class QuestionPolicy
{
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
}
