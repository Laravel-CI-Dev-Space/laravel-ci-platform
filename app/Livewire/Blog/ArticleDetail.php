<?php

declare(strict_types=1);

namespace App\Livewire\Blog;

use App\Services\Blog\ArticleService;
use Illuminate\View\View;
use Livewire\Component;

class ArticleDetail extends Component
{
    public string $articleSlug = '';

    /**
     * Initialise le slug depuis le paramètre passé par la vue parente.
     */
    public function mount(string $slug): void
    {
        $this->articleSlug = $slug;
    }

    public function render(ArticleService $articleService): View
    {
        $article         = $articleService->getBySlug($this->articleSlug);
        $similarArticles = $articleService->getSimilarArticles($article);

        return view('livewire.blog.article-detail', compact('article', 'similarArticles'));
    }
}
