<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Answer;
use App\Models\Article;
use App\Models\Comment;
use App\Models\Question;
use App\Services\NotificationService;

class CommentObserver
{
    public function __construct(private readonly NotificationService $notifications) {}

    public function created(Comment $comment): void
    {
        $commentable = $comment->commentable;

        $url = match (true) {
            $commentable instanceof Article  => route('blog.show', $commentable->slug),
            $commentable instanceof Question => route('forum.show', $commentable->slug),
            $commentable instanceof Answer   => route('forum.show', $commentable->question?->slug),
            default                          => url('/'),
        };

        $this->notifications->notifyAdmins('new_comment', [
            'message' => "Nouveau commentaire de {$comment->user?->name}",
            'url'     => $url,
            'icon'    => 'fa-solid fa-message',
        ]);
    }
}
