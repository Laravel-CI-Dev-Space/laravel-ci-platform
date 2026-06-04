<?php

declare(strict_types=1);

namespace App\Livewire\Blog;

use App\Enums\UserRole;
use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use App\Services\Forum\CommentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class CommentSection extends Component
{
    use WithPagination;

    public int $articleId;

    public string $body = '';

    public ?int $replyingTo = null;

    public string $replyBody = '';

    public function mount(Article $article): void
    {
        $this->articleId = $article->id;
    }

    protected function rules(): array
    {
        return [
            'body' => ['required', 'string', 'min:5', 'max:500'],
        ];
    }

    protected function messages(): array
    {
        return [
            'body.required' => 'Le commentaire est obligatoire.',
            'body.min'      => 'Le commentaire doit contenir au moins :min caractères.',
            'body.max'      => 'Le commentaire ne peut pas dépasser :max caractères.',
        ];
    }

    /**
     * Soumet un nouveau commentaire de niveau racine.
     */
    public function addComment(CommentService $commentService): void
    {
        abort_unless(Auth::check(), 403);
        $this->validate();

        /** @var User $user */
        $user    = Auth::user();
        $article = Article::findOrFail($this->articleId);

        $commentService->createComment($user, $article, ['body' => $this->body]);

        $this->body = '';
        $this->resetPage();

        $this->dispatch('comment-added');
    }

    /**
     * Prépare la réponse à un commentaire existant.
     */
    public function startReply(int $commentId): void
    {
        $this->replyingTo = $commentId;
        $this->replyBody  = '';
    }

    /**
     * Annule la réponse en cours.
     */
    public function cancelReply(): void
    {
        $this->replyingTo = null;
        $this->replyBody  = '';
    }

    /**
     * Soumet une réponse à un commentaire.
     */
    public function addReply(CommentService $commentService): void
    {
        abort_unless(Auth::check() && $this->replyingTo !== null, 403);

        $this->validateOnly('replyBody', [
            'replyBody' => ['required', 'string', 'min:5', 'max:500'],
        ], [
            'replyBody.required' => 'La réponse est obligatoire.',
            'replyBody.min'      => 'La réponse doit contenir au moins :min caractères.',
            'replyBody.max'      => 'La réponse ne peut pas dépasser :max caractères.',
        ]);

        /** @var User $user */
        $user    = Auth::user();
        $article = Article::findOrFail($this->articleId);

        $commentService->createComment($user, $article, [
            'body'      => $this->replyBody,
            'parent_id' => $this->replyingTo,
        ]);

        $this->replyingTo = null;
        $this->replyBody  = '';
    }

    /**
     * Supprime un commentaire (auteur ou modérateur).
     */
    public function deleteComment(int $commentId): void
    {
        /** @var User|null $user */
        $user = Auth::user();

        abort_unless($user !== null, 403);

        $comment = Comment::findOrFail($commentId);

        abort_unless(
            $comment->isOwnedBy($user) || $user->hasAnyRole([
                UserRole::Admin->value,
                UserRole::Moderator->value,
                UserRole::SuperAdmin->value,
            ]),
            403,
        );

        $comment->delete();

        Article::where('id', $this->articleId)->decrement('comments_count');
    }

    public function render(CommentService $commentService): View
    {
        $article  = Article::findOrFail($this->articleId);
        $comments = $commentService->getComments($article, 10);

        return view('livewire.blog.comment-section', compact('article', 'comments'));
    }
}
