<?php

declare(strict_types=1);

namespace App\Services\Search;

use App\Models\Article;
use App\Models\JobOffer;
use App\Models\Question;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class SearchService
{
    /**
     * Recherche globale sur questions, articles et offres d'emploi.
     *
     * @return array{questions: Collection<int, Question>, articles: Collection<int, Article>, jobs: Collection<int, JobOffer>, total: int}
     */
    public function globalSearch(string $query, int $limit = 5): array
    {
        if (mb_strlen(trim($query)) < 2) {
            return [
                'questions' => collect(),
                'articles'  => collect(),
                'jobs'      => collect(),
                'total'     => 0,
            ];
        }

        $questions = Question::search($query)
            ->where('status', 'published')
            ->take($limit)
            ->get();

        $articles = Article::search($query)
            ->where('status', 'published')
            ->take($limit)
            ->get();

        $jobs = JobOffer::search($query)
            ->where('status', 'active')
            ->take($limit)
            ->get();

        return [
            'questions' => $questions,
            'articles'  => $articles,
            'jobs'      => $jobs,
            'total'     => $questions->count() + $articles->count() + $jobs->count(),
        ];
    }

    /**
     * Recherche paginée sur une entité spécifique (ou toutes).
     *
     * @param  string  $type  questions|articles|jobs|all
     * @return array{results: Collection<int, mixed>|LengthAwarePaginator, total: int}
     */
    public function search(string $query, string $type = 'all', int $perPage = 15): array
    {
        if (mb_strlen(trim($query)) < 2) {
            return ['results' => collect(), 'total' => 0];
        }

        $results = match ($type) {
            'questions' => Question::search($query)->where('status', 'published')->paginate($perPage),
            'articles'  => Article::search($query)->where('status', 'published')->paginate($perPage),
            'jobs'      => JobOffer::search($query)->where('status', 'active')->paginate($perPage),
            default     => $this->searchAll($query, $perPage),
        };

        return [
            'results' => $results,
            'total'   => $results instanceof LengthAwarePaginator ? $results->total() : $results->count(),
        ];
    }

    /**
     * Recherche toutes les entités et fusionne les résultats.
     *
     * @return Collection<int, array{type: string, model: Question|Article|JobOffer}>
     */
    private function searchAll(string $query, int $perPage): Collection
    {
        $questions = Question::search($query)->where('status', 'published')->take($perPage)->get()
            ->map(fn (Question $question): array => ['type' => 'question', 'model' => $question]);

        $articles = Article::search($query)->where('status', 'published')->take($perPage)->get()
            ->map(fn (Article $article): array => ['type' => 'article', 'model' => $article]);

        $jobs = JobOffer::search($query)->where('status', 'active')->take($perPage)->get()
            ->map(fn (JobOffer $job): array => ['type' => 'job', 'model' => $job]);

        // ->toBase() : les collections Eloquent surchargent merge() et appellent
        // getKey() sur chaque élément, ce qui échoue puisque nos éléments sont
        // des tableaux ['type' => ..., 'model' => ...] et non des modèles.
        return $questions->toBase()->merge($articles->toBase())->merge($jobs->toBase());
    }

    /**
     * Suggestions rapides pour l'autocomplete (max 3 par type).
     *
     * @return array{questions: Collection<int, Question>, articles: Collection<int, Article>, jobs: Collection<int, JobOffer>, total: int}
     */
    public function suggest(string $query): array
    {
        return $this->globalSearch($query, 3);
    }
}
