<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Question;
use App\Services\NotificationService;

class QuestionObserver
{
    public function __construct(private readonly NotificationService $notifications) {}

    public function created(Question $question): void
    {
        $this->notifications->notifyAdmins('new_question', [
            'message' => "Nouvelle question : « {$question->title} » par {$question->user?->name}",
            'url'     => route('forum.show', $question->slug),
            'icon'    => 'fa-solid fa-circle-question',
        ]);
    }
}
