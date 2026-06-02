<?php

declare(strict_types=1);

namespace App\Livewire\Blog;

use App\Models\Tag;
use App\Models\User;
use App\Services\Blog\ArticleService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;

class SubmitArticle extends Component
{
    use WithFileUploads;

    public string $title = '';

    public string $excerpt = '';

    public string $body = '';

    public string $level = 'beginner';

    public array $selectedTags = [];

    public bool $preview = false;

    public mixed $coverImage = null;

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
            'title.max'             => 'Le titre ne peut pas dépasser :max caractères.',
            'excerpt.max'           => "L'extrait ne peut pas dépasser :max caractères.",
            'body.required'         => "Le corps de l'article est obligatoire.",
            'body.min'              => "L'article doit contenir au moins :min caractères.",
            'level.required'        => 'Le niveau est obligatoire.',
            'level.in'              => 'Le niveau doit être débutant, intermédiaire ou avancé.',
            'selectedTags.required' => 'Veuillez sélectionner au moins un tag.',
            'selectedTags.min'      => 'Veuillez sélectionner au moins :min tag.',
            'selectedTags.max'      => 'Vous ne pouvez pas sélectionner plus de :max tags.',
            'selectedTags.*.exists' => 'Un des tags sélectionnés est invalide.',
            'coverImage.image'      => "L'image de couverture doit être une image valide.",
            'coverImage.max'        => "L'image de couverture ne peut pas dépasser 2 Mo.",
        ];
    }

    /**
     * Bascule le mode prévisualisation.
     */
    public function togglePreview(): void
    {
        $this->preview = ! $this->preview;
    }

    /**
     * Ajoute un tag à la sélection (max 5).
     */
    public function addTag(int $tagId): void
    {
        if (count($this->selectedTags) >= 5) {
            return;
        }

        if (! in_array($tagId, $this->selectedTags, true)) {
            $this->selectedTags[] = $tagId;
        }
    }

    /**
     * Retire un tag de la sélection.
     */
    public function removeTag(int $tagId): void
    {
        $this->selectedTags = array_values(
            array_filter($this->selectedTags, fn (int $id): bool => $id !== $tagId),
        );
    }

    /**
     * Valide et crée l'article en brouillon, puis redirige vers le dashboard.
     */
    public function save(ArticleService $articleService): void
    {
        $this->validate();

        /** @var User $user */
        $user = Auth::user();

        $articleService->createArticle(
            user: $user,
            data: [
                'title'   => $this->title,
                'excerpt' => $this->excerpt ?: null,
                'body'    => $this->body,
                'level'   => $this->level,
                'tags'    => $this->selectedTags,
            ],
            coverImage: $this->coverImage,
        );

        session()->flash('success', 'Votre article a été sauvegardé en brouillon.');

        $this->redirect(route('dashboard.member.articles'), navigate: true);
    }

    public function render(): View
    {
        $tags = Tag::whereIn('scope', ['blog', 'both'])
            ->orderByDesc('usage_count')
            ->get();

        return view('livewire.blog.submit-article', compact('tags'));
    }
}
