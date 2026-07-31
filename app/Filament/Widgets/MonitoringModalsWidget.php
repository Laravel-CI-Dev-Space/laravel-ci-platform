<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\UserRole;
use App\Models\Analytics\PageView;
use Carbon\Carbon;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;

class MonitoringModalsWidget extends Widget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = 0;

    protected string $view = 'filament.widgets.monitoring-modals';

    public bool $open = false;

    public string $type = '';

    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole([
            UserRole::SuperAdmin->value,
            UserRole::SuperAdmin->value,
        ]) ?? false;
    }

    #[On('monitoring-open')]
    public function openModal(string $type): void
    {
        $this->type  = $type;
        $this->open  = true;
    }

    public function close(): void
    {
        $this->open = false;
    }

    public function getRowsProperty(): array|Collection
    {
        return match ($this->type) {
            'jobs'   => $this->jobsData(),
            'slow'   => $this->requestsData('slow'),
            'errors' => $this->requestsData('errors'),
            'routes' => $this->routesData(),
            default  => [],
        };
    }

    public function getHeadingProperty(): string
    {
        return match ($this->type) {
            'jobs'   => 'File d\'attente & Jobs échoués',
            'slow'   => 'Requêtes lentes (>500ms) - 24h',
            'errors' => 'Erreurs 4xx/5xx - 24h',
            'routes' => 'Performance par route - 24h',
            default  => 'Détail',
        };
    }

    // ── Data loaders ────────────────────────────────────────

    private function jobsData(): array
    {
        $pending = DB::table('jobs')->orderBy('created_at')->limit(200)->get()
            ->map(fn (object $j): array => [
                'queue'        => $j->queue,
                'class'        => $this->jobClass($j->payload),
                'attempts'     => $j->attempts,
                'available_at' => $j->available_at
                    ? Carbon::createFromTimestamp($j->available_at)->diffForHumans()
                    : '-',
                'created_at'   => Carbon::createFromTimestamp($j->created_at)->diffForHumans(),
            ])->toArray();

        $failed = DB::table('failed_jobs')->orderBy('failed_at', 'desc')->limit(200)->get()
            ->map(fn (object $j): array => [
                'queue'     => $j->queue,
                'class'     => $this->jobClass($j->payload),
                'uuid'      => $j->uuid,
                'error'     => $this->firstLine($j->exception ?? ''),
                'failed_at' => Carbon::parse($j->failed_at)->diffForHumans(),
            ])->toArray();

        return compact('pending', 'failed');
    }

    private function requestsData(string $mode): Collection
    {
        $since = now()->subHours(24);

        return match ($mode) {
            'errors' => PageView::where('status_code', '>=', 400)
                ->where('created_at', '>=', $since)
                ->orderByDesc('created_at')
                ->limit(300)
                ->get(['method', 'path', 'status_code', 'duration_ms', 'route_name', 'created_at']),

            default => PageView::whereNotNull('duration_ms')
                ->where('duration_ms', '>', 500)
                ->where('created_at', '>=', $since)
                ->orderByDesc('duration_ms')
                ->limit(300)
                ->get(['method', 'path', 'status_code', 'duration_ms', 'route_name', 'created_at']),
        };
    }

    private function routesData(): Collection
    {
        return PageView::whereNotNull('duration_ms')
            ->where('created_at', '>=', now()->subHours(24))
            ->selectRaw('COALESCE(route_name, path) as label, COUNT(*) as hits, ROUND(AVG(duration_ms)) as avg_ms, MAX(duration_ms) as max_ms, SUM(CASE WHEN status_code >= 400 THEN 1 ELSE 0 END) as errors')
            ->groupBy('route_name', 'path')
            ->orderByDesc('avg_ms')
            ->limit(100)
            ->get();
    }

    private function jobClass(string $payload): string
    {
        $data = json_decode($payload, true) ?? [];
        return $data['displayName'] ?? ($data['job'] ?? 'Unknown');
    }

    private function firstLine(string $text): string
    {
        $line = trim(strtok($text, "\n") ?: $text);
        return strlen($line) > 120 ? substr($line, 0, 120) . '…' : $line;
    }

    public function retryAllFailed(): void
    {
        \Illuminate\Support\Facades\Artisan::call('queue:retry', ['id' => ['all']]);
    }

    public function flushFailed(): void
    {
        DB::table('failed_jobs')->truncate();
    }
}
