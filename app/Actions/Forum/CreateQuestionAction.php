<?php

declare(strict_types=1);

namespace App\Actions\Forum;

use App\Models\Question;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

final class CreateQuestionAction
{
    /**
     * @param  array{title: string, content: string}  $data
     */
    public function execute(User $author, array $data): Question
    {
        Gate::forUser($author)->authorize('create', Question::class);

        return DB::transaction(fn (): Question => Question::create([
            'user_id' => $author->id,
            'title'   => $data['title'],
            'slug'    => Question::generateSlug($data['title']),
            'content' => $data['content'],
            'pinned'  => false,
            'closed'  => false,
        ]));
    }
}
