<?php

declare(strict_types=1);

namespace App\Services\Forum;

use App\Models\Question;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class QuestionService
{
    /**
     * Récupère les questions paginées avec filtres et tri.
     */
    public function getQuestions(
        string $sort = 'recent',
        ?int $tagId = null,
        string $search = '',
        int $perPage = 20,
    ): LengthAwarePaginator {
        $query = Question::published()
            ->with(['user', 'tags'])
            ->orderByDesc('is_pinned');

        if ($tagId !== null) {
            $query->whereHas('tags', fn ($q) => $q->where('tags.id', $tagId));
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('body', 'like', "%{$search}%");
            });
        }

        return match ($sort) {
            'votes'      => $query->orderByDesc('votes_score')->paginate($perPage),
            'unanswered' => $query->unanswered()->orderByDesc('last_activity_at')->paginate($perPage),
            default      => $query->orderByDesc('last_activity_at')->paginate($perPage),
        };
    }

    /**
     * Crée une nouvelle question et attache les tags.
     */
    public function createQuestion(User $user, array $data): Question
    {
        $question = Question::create([
            'user_id'          => $user->id,
            'title'            => $data['title'],
            'slug'             => $this->generateUniqueSlug($data['title']),
            'body'             => $data['body'],
            'body_html'        => $this->parseMarkdown($data['body']),
            'status'           => 'published',
            'last_activity_at' => now(),
        ]);

        $question->tags()->sync($data['tags']);

        Tag::whereIn('id', $data['tags'])->increment('usage_count');

        return $question;
    }

    /**
     * Récupère une question par son slug avec eager loading.
     */
    public function getBySlug(string $slug): Question
    {
        return Question::with(['user', 'tags', 'answers.user', 'acceptedAnswer'])
            ->where('slug', $slug)
            ->where('status', '!=', 'deleted')
            ->firstOrFail();
    }

    /**
     * Incrémente le compteur de vues (une seule fois par session).
     */
    public function incrementViews(Question $question): void
    {
        $key = "question_viewed_{$question->id}";

        if (! session()->has($key)) {
            $question->increment('views_count');
            session()->put($key, true);
        }
    }

    /**
     * Épingle ou désépingle une question (admin/moderator only).
     */
    public function togglePin(Question $question): void
    {
        $question->update(['is_pinned' => ! $question->is_pinned]);
    }

    /**
     * Cache ou publie une question (modération).
     */
    public function toggleVisibility(Question $question): void
    {
        $newStatus = $question->status === 'published' ? 'hidden' : 'published';
        $question->update(['status' => $newStatus]);
    }

    /**
     * Met à jour le titre, le corps et les tags d'une question existante.
     */
    public function updateQuestion(Question $question, array $data): Question
    {
        $question->update([
            'title'     => $data['title'],
            'body'      => $data['body'],
            'body_html' => $this->parseMarkdown($data['body']),
            'edited_at' => now(),
        ]);

        $question->tags()->sync($data['tags'] ?? []);

        return $question;
    }

    private function generateUniqueSlug(string $title): string
    {
        $slug     = Str::slug($title);
        $original = $slug;
        $count    = 1;

        while (Question::where('slug', $slug)->exists()) {
            $slug = "{$original}-{$count}";
            $count++;
        }

        return $slug;
    }

    private function parseMarkdown(string $body): string
    {
        $paragraphs = array_filter(array_map('trim', explode("\n\n", $body)));

        if (empty($paragraphs)) {
            return '<p>' . nl2br(e($body)) . '</p>';
        }

        return implode('', array_map(
            static fn (string $p): string => '<p>' . nl2br(e($p)) . '</p>',
            $paragraphs,
        ));
    }
}
