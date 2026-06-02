<?php

declare(strict_types=1);

namespace App\Services\Blog;

use App\Models\Article;
use App\Models\Tag;
use App\Models\User;
use App\Services\AssetService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;

class ArticleService
{
    public function __construct(private readonly AssetService $assetService) {}

    /**
     * Récupère les articles publiés avec filtres et pagination.
     *
     * @param  string  $level  — all/beginner/intermediate/advanced
     * @param  int|null  $tagId  — filtre par tag
     * @param  string  $sort  — recent/popular/most-viewed
     */
    public function getArticles(
        string $level = 'all',
        ?int $tagId = null,
        string $sort = 'recent',
        int $perPage = 12,
    ): LengthAwarePaginator {
        $query = Article::published()->with(['author', 'tags']);

        if ($level !== 'all') {
            $query->where('level', $level);
        }

        if ($tagId !== null) {
            $query->whereHas('tags', fn ($q) => $q->where('tags.id', $tagId));
        }

        return match ($sort) {
            'popular'     => $query->orderByDesc('comments_count')->paginate($perPage),
            'most-viewed' => $query->orderByDesc('views_count')->paginate($perPage),
            default       => $query->orderByDesc('published_at')->paginate($perPage),
        };
    }

    /**
     * Récupère un article publié par son slug.
     * Eager loads : author, tags, comments.
     */
    public function getBySlug(string $slug): Article
    {
        return Article::with(['author', 'tags', 'comments.user'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();
    }

    /**
     * Crée un article en brouillon.
     * Attache les tags. Upload cover image si fournie via AssetService.
     */
    public function createArticle(User $user, array $data, mixed $coverImage = null): Article
    {
        $coverFilename = null;

        if ($coverImage !== null) {
            $coverFilename = $this->assetService->upload($coverImage, 'covers', 'cover', $user->id);
        }

        $article = Article::create([
            'user_id'     => $user->id,
            'title'       => $data['title'],
            'slug'        => $this->generateUniqueSlug($data['title']),
            'excerpt'     => $data['excerpt'] ?? null,
            'body'        => $data['body'],
            'body_html'   => $this->parseMarkdown($data['body']),
            'level'       => $data['level'],
            'cover_image' => $coverFilename,
            'status'      => 'draft',
        ]);

        $article->tags()->sync($data['tags'] ?? []);

        Tag::whereIn('id', $data['tags'] ?? [])->increment('usage_count');

        return $article;
    }

    /**
     * Met à jour un article existant.
     */
    public function updateArticle(Article $article, array $data, mixed $coverImage = null): Article
    {
        $coverFilename = $article->cover_image;

        if ($coverImage !== null) {
            $coverFilename = $this->assetService->upload(
                $coverImage,
                'covers',
                'cover',
                $article->user_id,
                $article->cover_image,
            );
        }

        $article->update([
            'title'       => $data['title'],
            'excerpt'     => $data['excerpt'] ?? null,
            'body'        => $data['body'],
            'body_html'   => $this->parseMarkdown($data['body']),
            'level'       => $data['level'],
            'cover_image' => $coverFilename,
            'edited_at'   => now(),
        ]);

        $article->tags()->sync($data['tags'] ?? []);

        return $article;
    }

    /**
     * Soumet un article pour validation admin (draft → pending).
     * Seul l'auteur peut soumettre son propre article.
     */
    public function submitForReview(User $user, Article $article): void
    {
        abort_unless($article->isOwnedBy($user), 403);
        abort_unless(in_array($article->status, ['draft', 'rejected'], true), 422);

        $article->update(['status' => 'pending']);
    }

    /**
     * Publie un article (pending → published).
     * Met à jour published_at et reviewed_at.
     */
    public function publish(User $reviewer, Article $article): void
    {
        $article->update([
            'status'       => 'published',
            'published_at' => now(),
            'reviewed_at'  => now(),
            'reviewed_by'  => $reviewer->id,
        ]);
    }

    /**
     * Rejette un article avec une raison (pending → rejected).
     */
    public function reject(User $reviewer, Article $article, string $reason): void
    {
        $article->update([
            'status'           => 'rejected',
            'rejection_reason' => $reason,
            'reviewed_at'      => now(),
            'reviewed_by'      => $reviewer->id,
        ]);
    }

    /**
     * Incrémente le compteur de vues (une seule fois par session).
     */
    public function incrementViews(Article $article): void
    {
        $key = "article_viewed_{$article->id}";

        if (! session()->has($key)) {
            $article->increment('views_count');
            session()->put($key, true);
        }
    }

    /**
     * Récupère les articles similaires (mêmes tags).
     */
    public function getSimilarArticles(Article $article, int $limit = 3): Collection
    {
        $tagIds = $article->tags->pluck('id');

        if ($tagIds->isEmpty()) {
            return Article::published()
                ->with(['author', 'tags'])
                ->where('id', '!=', $article->id)
                ->latest('published_at')
                ->limit($limit)
                ->get();
        }

        return Article::published()
            ->with(['author', 'tags'])
            ->where('id', '!=', $article->id)
            ->whereHas('tags', fn ($q) => $q->whereIn('tags.id', $tagIds))
            ->limit($limit)
            ->get();
    }

    /**
     * Récupère les articles en attente de validation.
     */
    public function getPendingArticles(): Collection
    {
        return Article::pending()
            ->with(['author', 'tags'])
            ->orderByDesc('created_at')
            ->get();
    }

    private function generateUniqueSlug(string $title): string
    {
        $slug     = Str::slug($title);
        $original = $slug;
        $count    = 1;

        while (Article::where('slug', $slug)->exists()) {
            $slug = "{$original}-{$count}";
            $count++;
        }

        return $slug;
    }

    private function parseMarkdown(string $body): string
    {
        $env = new Environment([
            'html_input'         => 'strip',
            'allow_unsafe_links' => false,
        ]);
        $env->addExtension(new CommonMarkCoreExtension);
        $env->addExtension(new GithubFlavoredMarkdownExtension);

        return (new MarkdownConverter($env))->convert($body)->getContent();
    }
}
