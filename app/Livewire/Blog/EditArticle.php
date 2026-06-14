<?php

declare(strict_types=1);

namespace App\Livewire\Blog;

use App\Models\Article;
use App\Models\Tag;
use App\Models\User;
use App\Services\Blog\ArticleService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;

class EditArticle extends Component
{
    use WithFileUploads;

    public int $articleId;

    public string $title = '';

    public string $excerpt = '';

    public string $body = '';

    public string $level = 'beginner';

    public array $selectedTags = [];

    public mixed $coverImage = null;

    public function mount(Article $article): void
    {
        /** @var User $user */
        $user = Auth::user();

        abort_unless($article->canEditBy($user), 403, 'Modification non autorisée ou délai de 48h dépassé.');

        $this->articleId    = $article->id;
        $this->title        = $article->title;
        $this->excerpt      = $article->excerpt ?? '';
        $this->body         = $article->body;
        $this->level        = $article->level->value;
        $this->selectedTags = $article->tags->pluck('id')->map(fn ($id) => (int) $id)->toArray();
    }

    protected function rules(): array
    {
        return [
            'title'          => ['required', 'string', 'min:10', 'max:300'],
            'excerpt'        => ['nullable', 'string', 'max:500'],
            'body'           => ['required', 'string', 'min:100'],
            'level'          => ['required', 'in:beginner,intermediate,advanced'],
            'selectedTags'   => ['required', 'array', 'min:1', 'max:5'],
            'selectedTags.*' => ['integer', 'exists:tags,id'],
            'coverImage'     => ['nullable', 'image', 'max:2048'],
        ];
    }

    protected function messages(): array
    {
        return [
            'title.required'        => 'Le titre est obligatoire.',
            'title.min'             => 'Le titre doit contenir au moins :min caractères.',
            'body.required'         => 'Le contenu est obligatoire.',
            'body.min'              => "L'article doit contenir au moins :min caractères.",
            'level.required'        => 'Le niveau est obligatoire.',
            'selectedTags.required' => 'Veuillez sélectionner au moins un tag.',
            'selectedTags.min'      => 'Veuillez sélectionner au moins :min tag.',
            'selectedTags.max'      => 'Vous ne pouvez pas sélectionner plus de :max tags.',
        ];
    }

    public function addTag(int $tagId): void
    {
        if (count($this->selectedTags) >= 5 || in_array($tagId, $this->selectedTags, true)) {
            return;
        }
        $this->selectedTags[] = $tagId;
    }

    public function removeTag(int $tagId): void
    {
        $this->selectedTags = array_values(
            array_filter($this->selectedTags, fn (int $id): bool => $id !== $tagId),
        );
    }

    public function save(ArticleService $articleService): void
    {
        $this->validate();

        /** @var User $user */
        $user    = Auth::user();
        $article = Article::findOrFail($this->articleId);

        abort_unless($article->canEditBy($user), 403);

        $articleService->updateArticle(
            article: $article,
            data: [
                'title'   => $this->title,
                'excerpt' => $this->excerpt ?: null,
                'body'    => $this->body,
                'level'   => $this->level,
                'tags'    => $this->selectedTags,
            ],
            coverImage: $this->coverImage,
        );

        session()->flash('success', 'Article modifié avec succès.');

        $this->redirect(route('dashboard.member.articles'), navigate: true);
    }

    public function render(): View
    {
        $article = Article::findOrFail($this->articleId);
        $tags    = Tag::whereIn('scope', ['blog', 'both'])->orderByDesc('usage_count')->get();

        return view('livewire.blog.edit-article', compact('article', 'tags'));
    }
}
