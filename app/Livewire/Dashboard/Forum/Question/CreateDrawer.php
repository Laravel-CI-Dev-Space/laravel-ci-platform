<?php

declare(strict_types=1);

namespace App\Livewire\Dashboard\Forum\Question;

use App\Actions\Forum\CreateQuestionAction;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class CreateDrawer extends Component
{
    public bool $open = false;

    public string $title = '';

    public string $body = '';

    #[On('open-create-drawer')]
    public function openDrawer(): void
    {
        $this->open = true;
    }

    public function closeDrawer(): void
    {
        $this->reset(['title', 'body']);
        $this->resetValidation();
        $this->open = false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        return [
            'title'   => ['required', 'string', 'min:10', 'max:255'],
            'body' => ['required', 'string', 'min:30'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'title.required'   => 'Le titre est obligatoire.',
            'title.min'        => 'Le titre doit contenir au moins :min caractères.',
            'title.max'        => 'Le titre ne peut pas dépasser :max caractères.',
            'body.required' => 'Le contenu est obligatoire.',
            'body.min'      => 'Le contenu doit contenir au moins :min caractères.',
        ];
    }

    public function save(CreateQuestionAction $action): void
    {
        $this->validate();

        /** @var User $user */
        $user = auth()->user();

        $question = $action->execute($user, [
            'title'   => $this->title,
            'body' => $this->body,
        ]);

        $this->closeDrawer();
        $this->dispatch('question-created', id: $question->id);
    }

    public function render(): View
    {
        return view('livewire.dashboard.forum.question.create-drawer');
    }
}
