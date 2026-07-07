<?php

declare(strict_types=1);

namespace App\AI\Tools;

use App\Models\Article;
use App\Models\JobOffer;
use App\Models\Question;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class ChatToolRegistry
{
    private const CACHE_TTL = 180; // 3 minutes

    public function __construct(private readonly User $user) {}

    // ── Définitions des intents (format OpenAI function calling) ─

    public function definitions(): array
    {
        return [
            [
                'name'        => 'search_questions',
                'description' => 'Recherche des questions dans le forum de la plateforme Laravel CI. Utilise cet intent quand l\'utilisateur demande des questions sur un sujet, cherche de l\'aide ou veut voir les discussions existantes.',
                'parameters'  => [
                    'type'       => 'object',
                    'properties' => [
                        'query' => [
                            'type'        => 'string',
                            'description' => 'Termes de recherche (titre, mots-clés)',
                        ],
                        'tag' => [
                            'type'        => 'string',
                            'description' => 'Filtrer par tag (ex: laravel, eloquent, vue)',
                        ],
                        'limit' => [
                            'type'        => 'integer',
                            'description' => 'Nombre de résultats (1-10, défaut 5)',
                        ],
                    ],
                    'required' => ['query'],
                ],
            ],
            [
                'name'        => 'search_articles',
                'description' => 'Recherche des articles publiés sur la plateforme. Utilise cet intent pour trouver des tutoriels, guides, retours d\'expérience sur Laravel/PHP.',
                'parameters'  => [
                    'type'       => 'object',
                    'properties' => [
                        'query' => [
                            'type'        => 'string',
                            'description' => 'Termes de recherche (titre, résumé)',
                        ],
                        'limit' => [
                            'type'        => 'integer',
                            'description' => 'Nombre de résultats (1-10, défaut 5)',
                        ],
                    ],
                    'required' => ['query'],
                ],
            ],
            [
                'name'        => 'get_member_stats',
                'description' => 'Récupère les statistiques d\'un membre de la communauté (points, grade, contributions). Sans paramètre, retourne les stats de l\'utilisateur connecté.',
                'parameters'  => [
                    'type'       => 'object',
                    'properties' => [
                        'username' => [
                            'type'        => 'string',
                            'description' => 'Nom d\'utilisateur GitHub (ex: Ky-Wilson). Optionnel — défaut: utilisateur connecté.',
                        ],
                    ],
                    'required' => [],
                ],
            ],
            [
                'name'        => 'get_job_offers',
                'description' => 'Liste les offres d\'emploi actives sur la plateforme (CDI, CDD, stage, freelance). Filtrable par mot-clé.',
                'parameters'  => [
                    'type'       => 'object',
                    'properties' => [
                        'keyword' => [
                            'type'        => 'string',
                            'description' => 'Filtre par titre ou description (ex: Laravel, remote, stage)',
                        ],
                        'contract_type' => [
                            'type'        => 'string',
                            'description' => 'Type de contrat: CDI, CDD, Freelance, Stage',
                        ],
                        'limit' => [
                            'type'        => 'integer',
                            'description' => 'Nombre de résultats (1-10, défaut 5)',
                        ],
                    ],
                    'required' => [],
                ],
            ],
        ];
    }

    // ── Dispatch ─────────────────────────────────────────────────

    public function execute(string $toolName, array $params): mixed
    {
        $cacheKey = "chat_tool_{$toolName}_" . md5(json_encode($params) . $this->user->id);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($toolName, $params) {
            return match ($toolName) {
                'search_questions' => $this->searchQuestions($params),
                'search_articles'  => $this->searchArticles($params),
                'get_member_stats' => $this->getMemberStats($params),
                'get_job_offers'   => $this->getJobOffers($params),
                default            => ['error' => "Intent «{$toolName}» inconnu."],
            };
        });
    }

    // ── Intent : search_questions ─────────────────────────────────

    private function searchQuestions(array $params): array
    {
        $query = trim($params['query'] ?? '');
        $tag   = trim($params['tag']   ?? '');
        $limit = min((int) ($params['limit'] ?? 5), 10);

        if (empty($query)) {
            return ['error' => 'Le paramètre query est requis.'];
        }

        $q = Question::with(['user:id,name,github_username', 'tags:id,name'])
            ->where('status', 'open')
            ->where(function ($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                  ->orWhere('body', 'LIKE', "%{$query}%");
            });

        if ($tag) {
            $q->whereHas('tags', fn ($t) => $t->where('name', 'LIKE', "%{$tag}%"));
        }

        $questions = $q->orderByDesc('last_activity_at')
            ->limit($limit)
            ->get();

        if ($questions->isEmpty()) {
            return ['count' => 0, 'results' => [], 'message' => "Aucune question trouvée pour «{$query}»."];
        }

        return [
            'count'   => $questions->count(),
            'results' => $questions->map(fn ($q) => [
                'title'        => $q->title,
                'url'          => url("/forum/{$q->slug}"),
                'votes'        => $q->votes_score,
                'answers'      => $q->answers_count,
                'is_solved'    => (bool) $q->accepted_answer_id,
                'tags'         => $q->tags->pluck('name')->toArray(),
                'author'       => $q->user->name ?? 'Inconnu',
                'asked'        => $q->created_at->diffForHumans(),
            ])->toArray(),
        ];
    }

    // ── Intent : search_articles ──────────────────────────────────

    private function searchArticles(array $params): array
    {
        $query = trim($params['query'] ?? '');
        $limit = min((int) ($params['limit'] ?? 5), 10);

        if (empty($query)) {
            return ['error' => 'Le paramètre query est requis.'];
        }

        $articles = Article::with(['user:id,name,github_username'])
            ->where('status', 'published')
            ->where(function ($q) use ($query) {
                $q->where('title',   'LIKE', "%{$query}%")
                  ->orWhere('excerpt', 'LIKE', "%{$query}%")
                  ->orWhere('body',    'LIKE', "%{$query}%");
            })
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();

        if ($articles->isEmpty()) {
            return ['count' => 0, 'results' => [], 'message' => "Aucun article trouvé pour «{$query}»."];
        }

        return [
            'count'   => $articles->count(),
            'results' => $articles->map(fn ($a) => [
                'title'        => $a->title,
                'url'          => url("/blog/{$a->slug}"),
                'excerpt'      => $a->excerpt ? mb_strimwidth($a->excerpt, 0, 200, '…') : null,
                'author'       => $a->user->name ?? 'Inconnu',
                'published_at' => $a->published_at?->diffForHumans(),
                'views'        => $a->views_count,
            ])->toArray(),
        ];
    }

    // ── Intent : get_member_stats ─────────────────────────────────

    private function getMemberStats(array $params): array
    {
        $username = trim($params['username'] ?? '');

        $user = $username
            ? User::with(['profile.grade'])->where('github_username', $username)->first()
            : User::with(['profile.grade'])->find($this->user->id);

        if (! $user) {
            return ['error' => "Membre «{$username}» introuvable."];
        }

        $profile = $user->profile;

        return [
            'name'           => $user->name,
            'username'       => $user->github_username,
            'url'            => url("/members/{$user->github_username}"),
            'points'         => $profile?->points ?? 0,
            'grade'          => $profile?->grade?->name ?? 'Aucun',
            'questions'      => $user->questions()->count(),
            'answers'        => $user->answers()->count(),
            'articles'       => $user->articles()->where('status', 'published')->count(),
            'member_since'   => $user->created_at->format('F Y'),
        ];
    }

    // ── Intent : get_job_offers ───────────────────────────────────

    private function getJobOffers(array $params): array
    {
        $keyword       = trim($params['keyword']       ?? '');
        $contractType  = trim($params['contract_type'] ?? '');
        $limit         = min((int) ($params['limit'] ?? 5), 10);

        $q = JobOffer::where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });

        if ($keyword) {
            $q->where(function ($q) use ($keyword) {
                $q->where('title',       'LIKE', "%{$keyword}%")
                  ->orWhere('description', 'LIKE', "%{$keyword}%")
                  ->orWhere('tech_stack',  'LIKE', "%{$keyword}%");
            });
        }

        if ($contractType) {
            $q->where('contract_type', 'LIKE', "%{$contractType}%");
        }

        $offers = $q->orderByDesc('published_at')->limit($limit)->get();

        if ($offers->isEmpty()) {
            return ['count' => 0, 'results' => [], 'message' => 'Aucune offre d\'emploi active correspondant à votre recherche.'];
        }

        return [
            'count'   => $offers->count(),
            'results' => $offers->map(fn ($o) => [
                'title'         => $o->title,
                'url'           => url("/jobs/{$o->slug}"),
                'company'       => $o->company_name,
                'location'      => $o->location ?? ($o->is_remote ? 'Remote' : 'Non précisé'),
                'contract_type' => $o->contract_type,
                'is_remote'     => (bool) $o->is_remote,
                'is_urgent'     => (bool) $o->is_urgent,
                'salary'        => $o->salary_visible && $o->salary_min
                    ? "{$o->salary_min}–{$o->salary_max} {$o->currency}"
                    : null,
                'published'     => $o->published_at?->diffForHumans(),
            ])->toArray(),
        ];
    }
}
