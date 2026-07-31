<?php

declare(strict_types=1);

namespace App\Livewire\Blog;

use App\Models\Tag;
use App\Services\Blog\ArticleService;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ArticleList extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    #[Url]
    public string $level = 'all';

    #[Url]
    public ?int $tagId = null;

    #[Url]
    public string $sort = 'recent';

    public function updatedLevel(): void
    {
        $this->resetPage();
    }

    public function updatedTagId(): void
    {
        $this->resetPage();
    }

    public function updatedSort(): void
    {
        $this->resetPage();
    }

    public function render(ArticleService $articleService): View
    {
        $articles = $articleService->getArticles(
            level: $this->level,
            tagId: $this->tagId,
            sort: $this->sort,
        );

        $tags = Tag::whereIn('scope', ['blog', 'both'])
            ->orderByDesc('usage_count')
            ->get();

        return view('livewire.blog.article-list', compact('articles', 'tags'));
    }
}
