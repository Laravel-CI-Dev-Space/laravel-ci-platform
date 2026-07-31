<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class N1AuditCommand extends Command
{
    protected $signature = 'app:n1-audit
                            {--threshold=10 : Seuil d\'alerte (nb de requêtes SQL)}';

    protected $description = 'Audit N+1 : teste chaque route publique via le kernel Laravel et compte les requêtes SQL';

    private array $results = [];

    private function resolveRoutes(): array
    {
        $question = DB::table('questions')->where('status', 'published')->value('slug') ?? 'test';
        $article  = DB::table('articles')->where('status', 'published')->value('slug') ?? 'test';
        $event    = DB::table('events')->where('status', 'published')->value('slug') ?? 'test';
        $job      = DB::table('job_offers')->where('status', 'active')->value('slug') ?? 'test';
        $member   = DB::table('users')
            ->where('github_username', 'not like', 'lt_%')
            ->whereNotNull('github_username')
            ->value('github_username') ?? 'lt_user_0';

        return [
            'home'         => '/',
            'blog'         => '/blog',
            'blog.show'    => "/blog/{$article}",
            'forum'        => '/forum',
            'forum.show'   => "/forum/{$question}",
            'events'       => '/events',
            'events.show'  => "/events/{$event}",
            'jobs'         => '/jobs',
            'jobs.show'    => "/jobs/{$job}",
            'about'        => '/about',
            'search'       => '/search?q=laravel',
            'members.show' => "/members/{$member}",
            'resources'    => '/resources',
        ];
    }

    public function handle(): int
    {
        $threshold = (int) $this->option('threshold');

        $this->newLine();
        $this->line('  <fg=cyan>Laravel CI — Audit N+1 Queries</>');
        $this->line("  Seuil d'alerte : {$threshold} requetes SQL / page");
        $this->newLine();

        $host   = parse_url(config('app.url'), PHP_URL_HOST) ?: 'localhost';
        $kernel = app(\Illuminate\Contracts\Http\Kernel::class);

        foreach ($this->resolveRoutes() as $name => $path) {
            $this->line("  Analyse <fg=gray>{$name}</> ...");
            $this->measureRoute($kernel, $name, $path, $host);
        }

        $this->newLine();
        $this->renderTable($threshold);
        $this->renderSummary($threshold);

        return self::SUCCESS;
    }

    private function measureRoute(
        \Illuminate\Contracts\Http\Kernel $kernel,
        string $name,
        string $path,
        string $host,
    ): void {
        $queries    = [];
        $sqlNorms   = [];
        $duplicates = 0;

        // Écoute les queries SQL pour CE seul appel
        $listener = function ($query) use (&$queries, &$sqlNorms, &$duplicates) {
            $norm = preg_replace('/\b\d+\b/', '?', $query->sql);
            $norm = preg_replace("/('[^']*')/", '?', $norm);
            if (in_array($norm, $sqlNorms, true)) {
                $duplicates++;
            }
            $sqlNorms[] = $norm;
            $queries[]  = ['sql' => $query->sql, 'time' => $query->time, 'norm' => $norm];
        };

        DB::listen($listener);

        $parsedPath  = strtok($path, '?');
        $queryString = parse_url($path, PHP_URL_QUERY) ?? '';

        $request = \Illuminate\Http\Request::create(
            $parsedPath . ($queryString ? "?{$queryString}" : ''),
            'GET',
            [],
            [],
            [],
            [
                'HTTP_HOST'       => $host,
                'SERVER_PORT'     => '8000',
                'REQUEST_SCHEME'  => 'http',
            ]
        );

        $memBefore = memory_get_usage(true);
        $start     = microtime(true);
        $status    = 0;

        try {
            $response = $kernel->handle($request);
            $status   = $response->getStatusCode();
            $kernel->terminate($request, $response);
        } catch (\Throwable) {
            $status = 0;
        }

        $elapsed = round((microtime(true) - $start) * 1000);
        $memMb   = round((memory_get_usage(true) - $memBefore) / 1024 / 1024, 1);

        // Supprime le listener pour ne pas interférer avec le prochain appel
        $dispatcher = DB::getEventDispatcher();
        $dispatcher->forget('Illuminate\Database\Events\QueryExecuted');

        $this->results[$name] = [
            'path'       => $path,
            'status'     => $status,
            'queries'    => count($queries),
            'duplicates' => $duplicates,
            'time_ms'    => $elapsed,
            'mem_mb'     => $memMb,
            'slow'       => collect($queries)->where('time', '>', 50)->count(),
            'top3'       => collect($queries)->sortByDesc('time')->take(3)->values()->toArray(),
            'top_norms'  => collect($sqlNorms)->countBy()->sortDesc()->take(3)->toArray(),
        ];
    }

    private function renderTable(int $threshold): void
    {
        $rows = [];

        foreach ($this->results as $name => $r) {
            $q      = $r['queries'];
            $status = match (true) {
                $r['status'] === 200 => '<fg=green>200</>',
                $r['status'] === 302 => '<fg=yellow>302</>',
                default              => "<fg=red>{$r['status']}</>",
            };

            $qCell = $q >= $threshold
                ? "<fg=red;options=bold>{$q}</>"
                : ($q >= (int) ($threshold * 0.7) ? "<fg=yellow>{$q}</>" : "<fg=green>{$q}</>");

            $diag = [];
            if ($q >= $threshold) {
                $diag[] = '<fg=red>N+1 suspect</>';
            }
            if ($r['duplicates'] > 0) {
                $diag[] = "<fg=yellow>{$r['duplicates']} doublons</>";
            }
            if ($r['slow'] > 0) {
                $diag[] = "<fg=red>{$r['slow']} slow(>50ms)</>";
            }

            $rows[] = [
                $name,
                $status,
                $qCell,
                $r['duplicates'] > 0 ? "<fg=yellow>{$r['duplicates']}</>" : '<fg=green>0</>',
                $r['time_ms'] . 'ms',
                $r['mem_mb'] . 'MB',
                implode(' ', $diag) ?: '<fg=green>OK</>',
            ];
        }

        $this->table(
            ['Route', 'HTTP', 'SQL', 'Doublons', 'Temps', 'Mémoire', 'Diagnostic'],
            $rows
        );
    }

    private function renderSummary(int $threshold): void
    {
        $suspects = collect($this->results)
            ->filter(fn ($r) => $r['queries'] >= $threshold || $r['duplicates'] > 3)
            ->sortByDesc('queries');

        if ($suspects->isEmpty()) {
            $this->line("  <fg=green>Aucun probleme N+1 detecte (seuil : {$threshold} requetes).</>");
            $this->newLine();
            return;
        }

        $this->line('  <fg=red;options=bold>Pages suspectes detectees :</>');
        $this->newLine();

        foreach ($suspects as $name => $r) {
            $this->line("  <fg=red>▶ {$name}</> ({$r['path']})");
            $this->line("    SQL: <fg=red>{$r['queries']}</> requetes | Doublons: <fg=yellow>{$r['duplicates']}</> | {$r['time_ms']}ms");

            if (!empty($r['top_norms'])) {
                $this->line('    Requetes repetees :');
                foreach ($r['top_norms'] as $sql => $count) {
                    if ($count > 1) {
                        $this->line("      x{$count} " . mb_substr($sql, 0, 90));
                    }
                }
            }

            if (!empty($r['top3'])) {
                $this->line('    Top 3 requetes lentes :');
                foreach ($r['top3'] as $q) {
                    $this->line('      <fg=gray>' . round($q['time']) . 'ms  ' . mb_substr($q['sql'], 0, 100) . '</>');
                }
            }

            $this->newLine();
        }

        $this->line('  <fg=yellow>Conseil : ouvrez le Debugbar dans le navigateur pour voir le detail SQL complet.</>');
        $this->line('  <fg=gray>Debugbar actif sur : http://127.0.0.1:8000 (APP_DEBUG=true)</>');
        $this->newLine();
    }
}
