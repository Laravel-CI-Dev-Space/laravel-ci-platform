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
     * @param  array{title: string, body: string}  $data
     */
    public function execute(User $author, array $data): Question
    {
        Gate::forUser($author)->authorize('create', Question::class);

        return DB::transaction(fn (): Question => Question::create([
            'user_id'   => $author->id,
            'title'     => $data['title'],
            'slug'      => Question::generateSlug($data['title']),
            'body'      => $data['body'],
            'status'    => QuestionStatus::Published->value,
            'is_pinned' => false,
        ]));
    }
}
