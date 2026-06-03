<?php

declare(strict_types=1);

namespace App\Actions\Forum;

use App\Enums\Forum\QuestionStatus;
use App\Models\Question;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

final class CreateQuestionAction
{
    /**
     * @param User $user
     * @param array $data
     * @return Question
     */
    public function handle(User $user, array $data): Question
    {
        Gate::forUser($user)->authorize('create', Question::class);

        return DB::transaction(fn (): Question => Question::create([
            'user_id'   => $user->id,
            'title'     => $data['title'],
            'body'      => $data['body'],
            'status'    => QuestionStatus::Published->value,
            'is_pinned' => false,
        ]));
    }
}
