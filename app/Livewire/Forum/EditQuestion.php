<?php

declare(strict_types=1);

namespace App\Livewire\Forum;

use App\Models\Question;
use App\Models\Tag;
use App\Models\User;
use App\Services\Forum\QuestionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

class EditQuestion extends Component
{
    public string $slug = '';

    public string $title = '';

    public string $body = '';

    public array $selectedTags = [];

    public function mount(Question $question): void
    {
        /** @var User $user */
        $user = Auth::user();

        abort_unless($question->canEditBy($user), 403, 'Modification non autorisée ou délai de 48h dépassé.');

        $this->slug         = $question->slug;
        $this->title        = $question->title;
        $this->body         = $question->body;
        $this->selectedTags = $question->tags->pluck('id')->map(fn ($id) => (int) $id)->toArray();
    }

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
            'body.required'         => 'Le corps de la question est obligatoire.',
            'body.min'              => 'La question doit contenir au moins :min caractères.',
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

    public function save(QuestionService $questionService): void
    {
        $this->validate();

        /** @var User $user */
        $user     = Auth::user();
        $question = Question::where('slug', $this->slug)->firstOrFail();

        abort_unless($question->canEditBy($user), 403);

        $questionService->updateQuestion($question, [
            'title' => $this->title,
            'body'  => $this->body,
            'tags'  => $this->selectedTags,
        ]);

        session()->flash('success', 'Question modifiée avec succès.');

        $this->redirect(route('forum.show', $question->slug), navigate: true);
    }

    public function render(): View
    {
        $tags = Tag::whereIn('scope', ['forum', 'both'])->orderByDesc('usage_count')->get();

        return view('livewire.forum.edit-question', compact('tags'));
    }
}
