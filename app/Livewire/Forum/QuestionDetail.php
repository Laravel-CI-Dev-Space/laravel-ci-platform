<?php

declare(strict_types=1);

namespace App\Livewire\Forum;

use App\Services\Forum\QuestionService;
use Illuminate\View\View;
use Livewire\Component;

class QuestionDetail extends Component
{
    public string $questionSlug = '';

    public bool $showAnswerForm = false;

    public function mount(string $slug): void
    {
        $this->questionSlug = $slug;
    }

    public function toggleAnswerForm(): void
    {
        $this->showAnswerForm = ! $this->showAnswerForm;
    }

    public function render(QuestionService $questionService): View
    {
        $question = $questionService->getBySlug($this->questionSlug);

        return view('livewire.forum.question-detail', compact('question'));
    }
}
