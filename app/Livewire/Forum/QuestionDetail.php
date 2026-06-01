<?php

declare(strict_types=1);

namespace App\Livewire\Forum;

use App\Models\Answer;
use App\Models\User;
use App\Services\Forum\AnswerService;
use App\Services\Forum\QuestionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

class QuestionDetail extends Component
{
    public string $questionSlug = '';

    public bool $showAnswerForm = true;

    public function mount(string $slug): void
    {
        $this->questionSlug = $slug;
    }

    public function toggleAnswerForm(): void
    {
        $this->showAnswerForm = ! $this->showAnswerForm;
    }

    /**
     * Marque une réponse comme solution acceptée (auteur de la question uniquement).
     */
    public function acceptAnswer(int $answerId, AnswerService $answerService): void
    {
        /** @var User $user */
        $user   = Auth::user();
        $answer = Answer::findOrFail($answerId);

        $answerService->acceptAnswer($user, $answer);
    }

    /**
     * Retire le statut de solution acceptée.
     */
    public function unacceptAnswer(int $answerId, AnswerService $answerService): void
    {
        /** @var User $user */
        $user   = Auth::user();
        $answer = Answer::findOrFail($answerId);

        $answerService->unacceptAnswer($user, $answer);
    }

    public function render(QuestionService $questionService): View
    {
        $question = $questionService->getBySlug($this->questionSlug);

        return view('livewire.forum.question-detail', compact('question'));
    }
}
