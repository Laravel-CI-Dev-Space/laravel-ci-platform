<?php

declare(strict_types=1);

namespace App\Http\Controllers\Forum;

use App\Http\Controllers\Controller;
use App\Http\Requests\Forum\StoreAnswerRequest;
use App\Models\Question;
use App\Services\Forum\AnswerService;
use Illuminate\Http\RedirectResponse;

class AnswerController extends Controller
{
    public function __construct(private readonly AnswerService $answerService) {}

    /**
     * Enregistre une nouvelle réponse à une question.
     */
    public function store(StoreAnswerRequest $request, Question $question): RedirectResponse
    {
        $this->answerService->createAnswer(
            user: $request->user(),
            question: $question,
            data: $request->validated(),
        );

        return redirect()
            ->route('forum.show', $question->slug)
            ->with('success', 'Votre réponse a été publiée avec succès.');
    }
}
