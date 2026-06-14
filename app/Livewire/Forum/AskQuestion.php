<?php

declare(strict_types=1);

namespace App\Livewire\Forum;

use App\Livewire\Concerns\RateLimited;
use App\Models\Tag;
use App\Models\User;
use App\Services\Forum\QuestionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

class AskQuestion extends Component
{
    use RateLimited;

    public string $title = '';

    public string $body = '';

    public array $selectedTags = [];

    public bool $preview = false;

    protected function rules(): array
    {
        return [
            'title'          => ['required', 'string', 'min:10', 'max:300'],
            'body'           => ['required', 'string', 'min:30'],
            'selectedTags'   => ['required', 'array', 'min:1', 'max:5'],
            'selectedTags.*' => ['integer', 'exists:tags,id'],
        ];
    }

    protected function messages(): array
    {
        return [
            'title.required'        => 'Le titre est obligatoire.',
            'title.min'             => 'Le titre doit contenir au moins :min caractères.',
            'title.max'             => 'Le titre ne peut pas dépasser :max caractères.',
            'body.required'         => 'Le corps de la question est obligatoire.',
            'body.min'              => 'La question doit contenir au moins :min caractères.',
            'selectedTags.required' => 'Veuillez sélectionner au moins un tag.',
            'selectedTags.min'      => 'Veuillez sélectionner au moins :min tag.',
            'selectedTags.max'      => 'Vous ne pouvez pas sélectionner plus de :max tags.',
            'selectedTags.*.exists' => 'Un des tags sélectionnés est invalide.',
        ];
    }

    public function togglePreview(): void
    {
        $this->preview = ! $this->preview;
    }

    public function addTag(int $tagId): void
    {
        if (count($this->selectedTags) >= 5) {
            return;
        }

        if (! in_array($tagId, $this->selectedTags, true)) {
            $this->selectedTags[] = $tagId;
        }
    }

    public function removeTag(int $tagId): void
    {
        $this->selectedTags = array_values(
            array_filter($this->selectedTags, fn (int $id): bool => $id !== $tagId),
        );
    }

    public function save(QuestionService $questionService): void
    {
        if ($this->tooManyAttempts('forum.questions', 10)) {
            $this->addError('title', $this->rateLimitMessage('forum.questions'));

            return;
        }

        $this->validate();

        /** @var User $user */
        $user = Auth::user();

        $question = $questionService->createQuestion($user, [
            'title' => $this->title,
            'body'  => $this->body,
            'tags'  => $this->selectedTags,
        ]);

        session()->flash('success', 'Votre question a été publiée avec succès.');

        $this->redirect(route('forum.show', $question->slug), navigate: true);
    }

    public function render(): View
    {
        $tags = Tag::whereIn('scope', ['forum', 'both'])
            ->orderByDesc('usage_count')
            ->get();

        return view('livewire.forum.ask-question', compact('tags'));
    }
}
