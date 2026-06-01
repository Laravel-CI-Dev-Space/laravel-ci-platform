<?php

declare(strict_types=1);

namespace App\Http\Controllers\Forum;

use App\Http\Controllers\Controller;
use App\Http\Requests\Forum\StoreQuestionRequest;
use App\Models\Question;
use App\Models\Tag;
use App\Services\Forum\QuestionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class QuestionController extends Controller
{
    public function __construct(private readonly QuestionService $questionService) {}

    /**
     * Liste paginée des questions publiées.
     */
    public function index(): View
    {
        $questions = $this->questionService->getQuestions();
        $tags      = Tag::whereIn('scope', ['forum', 'both'])->orderByDesc('usage_count')->get();

        return view('web.forum.index', compact('questions', 'tags'));
    }

    /**
     * Formulaire de création d'une nouvelle question.
     */
    public function create(): View
    {
        $tags = Tag::whereIn('scope', ['forum', 'both'])->orderByDesc('usage_count')->get();

        return view('web.forum.ask', compact('tags'));
    }

    /**
     * Enregistre une nouvelle question.
     */
    public function store(StoreQuestionRequest $request): RedirectResponse
    {
        $question = $this->questionService->createQuestion(
            user: $request->user(),
            data: $request->validated(),
        );

        return redirect()
            ->route('forum.show', $question->slug)
            ->with('success', 'Votre question a été publiée avec succès.');
    }

    /**
     * Affiche le détail d'une question par son slug.
     */
    public function show(string $slug): View
    {
        $question = $this->questionService->getBySlug($slug);
        $this->questionService->incrementViews($question);

        return view('web.forum.show', compact('question', 'slug'));
    }

    /**
     * Redirige vers le formulaire de modification (réutilise AskQuestion).
     */
    public function edit(Question $question): RedirectResponse
    {
        abort_unless($question->isOwnedBy(request()->user()), 403);

        return redirect()->route('forum.show', $question->slug);
    }

    /**
     * Supprime (soft-delete) une question appartenant à l'utilisateur.
     */
    public function destroy(Question $question): RedirectResponse
    {
        abort_unless($question->isOwnedBy(request()->user()), 403);

        $question->delete();

        return redirect()
            ->route('forum.index')
            ->with('success', 'Votre question a été supprimée.');
    }
}
