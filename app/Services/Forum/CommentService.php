<?php

declare(strict_types=1);

namespace App\Services\Forum;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

class CommentService
{
    public function __construct(
        private readonly MentionService $mentionService,
    ) {}

    /**
     * Crée un commentaire sur une question ou réponse.
     */
    public function createComment(User $user, Model $commentable, array $data): Comment
    {
        $comment = Comment::create([
            'commentable_id'   => $commentable->id,
            'commentable_type' => $commentable->getMorphClass(),
            'user_id'          => $user->id,
            'parent_id'        => $data['parent_id'] ?? null,
            'body'             => $data['body'],
        ]);

        $commentable->increment('comments_count');

        $this->mentionService->processMentions($comment->body, $user, $comment);

        return $comment;
    }

    /**
     * Récupère les commentaires d'une entité avec pagination.
     */
    public function getComments(Model $commentable, int $perPage = 10): LengthAwarePaginator
    {
        return Comment::where('commentable_id', $commentable->id)
            ->where('commentable_type', $commentable->getMorphClass())
            ->whereNull('parent_id')
            ->where('is_hidden', false)
            ->with(['user', 'replies.user'])
            ->orderBy('created_at')
            ->paginate($perPage);
    }
}
