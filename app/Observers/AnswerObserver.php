<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Answer;
use App\Services\NotificationService;

class AnswerObserver
{
    public function __construct(private readonly NotificationService $notifications) {}

    public function created(Answer $answer): void
    {
        $this->notifications->notifyAdmins('new_answer_admin', [
            'message' => "Nouvelle réponse de {$answer->user?->name} sur : « {$answer->question?->title} »",
            'url'     => route('forum.show', $answer->question?->slug),
            'icon'    => 'fa-solid fa-comment',
        ]);
    }
}
