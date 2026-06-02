<?php

declare(strict_types=1);

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Blog\StoreArticleRequest;
use App\Models\Article;
use App\Models\Tag;
use App\Services\Blog\ArticleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function __construct(private readonly ArticleService $articleService) {}

    /**
     * Liste paginée des articles publiés avec filtres.
     */
    public function index(): View
    {
        $articles = $this->articleService->getArticles();
        $tags     = Tag::whereIn('scope', ['blog', 'both'])->orderByDesc('usage_count')->get();

        return view('web.blog.index', compact('articles', 'tags'));
    }

    /**
     * Affiche le détail d'un article par son slug.
     */
    public function show(string $slug): View
    {
        $article = $this->articleService->getBySlug($slug);
        $this->articleService->incrementViews($article);

        return view('web.blog.show', compact('article', 'slug'));
    }

    /**
     * Formulaire de soumission d'un nouvel article.
     */
    public function create(): View
    {
        $tags = Tag::whereIn('scope', ['blog', 'both'])->orderByDesc('usage_count')->get();

        return view('web.blog.submit', compact('tags'));
    }

    /**
     * Enregistre un nouvel article en brouillon.
     */
    public function store(StoreArticleRequest $request): RedirectResponse
    {
        $this->articleService->createArticle(
            user: $request->user(),
            data: $request->validated(),
            coverImage: $request->file('cover_image'),
        );

        return redirect()
            ->route('dashboard.member.articles')
            ->with('success', 'Votre article a été sauvegardé en brouillon.');
    }

    /**
     * Soumet un article pour validation par les modérateurs.
     */
    public function submit(Article $article): RedirectResponse
    {
        $this->articleService->submitForReview(request()->user(), $article);

        return redirect()
            ->route('dashboard.member.articles')
            ->with('success', 'Votre article a été soumis pour validation.');
    }

    /**
     * Supprime (soft-delete) un article appartenant à l'utilisateur connecté.
     */
    public function destroy(Article $article): RedirectResponse
    {
        abort_unless($article->isOwnedBy(request()->user()), 403);

        $article->delete();

        return redirect()
            ->route('dashboard.member.articles')
            ->with('success', 'Article supprimé avec succès.');
    }
}
