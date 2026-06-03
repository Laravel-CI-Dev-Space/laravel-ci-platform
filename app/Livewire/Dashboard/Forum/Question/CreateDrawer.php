<?php

declare(strict_types=1);

namespace App\Livewire\Dashboard\Forum\Question;

use App\Actions\Forum\CreateQuestionAction;
use App\Http\Requests\Forum\StoreQuestionRequest;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
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
        return (new StoreQuestionRequest)->rules();
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return (new StoreQuestionRequest)->messages();
    }

    public function save(CreateQuestionAction $action): void
    {
        $validated = $this->validate();

        // Auth::user()
        $question = $action->handle(User::first(), $validated);

        $this->closeDrawer();
        $this->dispatch('question-created', id: $question->id);
    }

    public function render(): View
    {
        return view('livewire.dashboard.forum.question.create-drawer');
    }
}

/*
    Probleme d'installation laravel Shopper

    Je débute avec Laravel Shopper, depuis plusieurs minutes
    je coince au niveau de l'installation, En effet je viens
    de fraichmenet installer Laravel 12.58.0 sur php 8.2,
    J'ai bien lancer la commande d... 
*/
