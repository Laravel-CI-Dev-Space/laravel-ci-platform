<?php

declare(strict_types=1);

namespace App\Services\Analytics;

use App\Models\Analytics\AnalyticsEvent;
use App\Models\Analytics\PageView;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class AnalyticsService
{
    private const SKIP_PATHS = [
        'admin', 'admin/*',
        'company/*',
        '_ignition/*',
        '_debugbar/*',
        'livewire/*',
        'up',
    ];

    private const SKIP_EXTENSIONS = ['js', 'css', 'map', 'ico', 'png', 'jpg', 'svg', 'woff', 'woff2', 'ttf'];

    /**
     * Records a page view from an incoming HTTP request.
     */
    public function trackPageView(Request $request): void
    {
        try {
            $path = $request->path();
            $ext  = pathinfo($path, PATHINFO_EXTENSION);

            if (in_array(strtolower($ext), self::SKIP_EXTENSIONS, strict: true)) {
                return;
            }

            foreach (self::SKIP_PATHS as $pattern) {
                if (fnmatch($pattern, $path)) {
                    return;
                }
            }

            $ua = $request->userAgent() ?? '';

            PageView::create([
                'session_id'   => session()->getId(),
                'user_id'      => auth()->id(),
                'path'         => '/' . $path,
                'query_string' => $request->getQueryString() ?: null,
                'referrer'     => $this->sanitizeUrl($request->header('Referer')),
                'device_type'  => $this->detectDevice($ua),
                'browser'      => $this->detectBrowser($ua),
                'created_at'   => now(),
            ]);
        } catch (\Throwable) {
            // Never let analytics break the app
        }
    }

    /**
     * Records a named interaction event.
     */
    public function trackEvent(
        string $type,
        ?int $userId = null,
        ?string $entityType = null,
        ?int $entityId = null,
        array $metadata = [],
    ): void {
        try {
            AnalyticsEvent::create([
                'session_id'  => session()->getId(),
                'user_id'     => $userId,
                'type'        => $type,
                'entity_type' => $entityType,
                'entity_id'   => $entityId,
                'metadata'    => $metadata ?: null,
                'created_at'  => now(),
            ]);
        } catch (\Throwable) {
            // Never let analytics break the app
        }
    }

    /**
     * Returns [$from, $to] Carbon pair for a period key.
     *
     * @return array{Carbon, Carbon}
     */
    public function parsePeriod(string $period): array
    {
        return match ($period) {
            '7d'         => [now()->subDays(7)->startOfDay(),   now()->endOfDay()],
            '30d'        => [now()->subDays(30)->startOfDay(),  now()->endOfDay()],
            '90d'        => [now()->subDays(90)->startOfDay(),  now()->endOfDay()],
            'today'      => [now()->startOfDay(),               now()->endOfDay()],
            '7d_prev'    => [now()->subDays(14)->startOfDay(),  now()->subDays(7)->startOfDay()],
            '30d_prev'   => [now()->subDays(61)->startOfDay(),  now()->subDays(31)->startOfDay()],
            '90d_prev'   => [now()->subDays(181)->startOfDay(), now()->subDays(91)->startOfDay()],
            'today_prev' => [now()->subDay()->startOfDay(),     now()->subDay()->endOfDay()],
            default      => [now()->subDays(30)->startOfDay(),  now()->endOfDay()],
        };
    }

    /** Total page views in the period. */
    public function getTotalPageViews(string $period): int
    {
        [$from, $to] = $this->parsePeriod($period);

        return PageView::whereBetween('created_at', [$from, $to])->count();
    }

    /** Unique visitor sessions in the period. */
    public function getUniqueVisitors(string $period): int
    {
        [$from, $to] = $this->parsePeriod($period);

        return PageView::whereBetween('created_at', [$from, $to])
            ->distinct('session_id')
            ->count('session_id');
    }

    /** Authenticated users who visited in the period. */
    public function getAuthenticatedVisitors(string $period): int
    {
        [$from, $to] = $this->parsePeriod($period);

        return PageView::whereBetween('created_at', [$from, $to])
            ->whereNotNull('user_id')
            ->distinct('user_id')
            ->count('user_id');
    }

    /**
     * Daily counts for a specific event type over the last N days (used for sparklines).
     *
     * @return array{labels: string[], data: int[]}
     */
    public function getEventSparkline(string $type, int $days = 7): array
    {
        $from = now()->subDays($days)->startOfDay();
        $to   = now()->endOfDay();

        $rows = AnalyticsEvent::selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->where('type', $type)
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('day')
            ->pluck('total', 'day');

        $labels = [];
        $data   = [];

        for ($date = $from->copy(); $date->lte($to); $date->addDay()) {
            $key      = $date->format('Y-m-d');
            $labels[] = $date->format('d/m');
            $data[]   = (int) ($rows[$key] ?? 0);
        }

        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * Daily page view counts for the period, keyed by date string.
     *
     * @return array{labels: string[], data: int[]}
     */
    public function getDailyPageViews(string $period): array
    {
        [$from, $to] = $this->parsePeriod($period);

        $rows = PageView::selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day');

        $labels = [];
        $data   = [];

        for ($date = $from->copy(); $date->lte($to); $date->addDay()) {
            $key      = $date->format('Y-m-d');
            $labels[] = $date->format('d/m');
            $data[]   = (int) ($rows[$key] ?? 0);
        }

        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * Top N most visited pages in the period.
     *
     * @return Collection<int, object{path: string, views: int, unique: int}>
     */
    public function getTopPages(string $period, int $limit = 10): Collection
    {
        [$from, $to] = $this->parsePeriod($period);

        return PageView::selectRaw('path, COUNT(*) as views, COUNT(DISTINCT session_id) as unique_visitors')
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('path')
            ->orderByDesc('views')
            ->limit($limit)
            ->get();
    }

    /**
     * Event counts grouped by type for the period.
     *
     * @return array<string, int>
     */
    public function getEventBreakdown(string $period): array
    {
        [$from, $to] = $this->parsePeriod($period);

        return AnalyticsEvent::selectRaw('type, COUNT(*) as total')
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('type')
            ->orderByDesc('total')
            ->pluck('total', 'type')
            ->map(fn ($v) => (int) $v)
            ->all();
    }

    /**
     * Daily event counts grouped by type for the period.
     *
     * @return array{labels: string[], datasets: array<int, array{label: string, data: int[]}>}
     */
    public function getDailyEvents(string $period): array
    {
        [$from, $to] = $this->parsePeriod($period);

        $rows = AnalyticsEvent::selectRaw('DATE(created_at) as day, type, COUNT(*) as total')
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('day', 'type')
            ->orderBy('day')
            ->get()
            ->groupBy('type');

        $labels = [];
        for ($date = $from->copy(); $date->lte($to); $date->addDay()) {
            $labels[] = $date->format('d/m');
        }

        $typeLabels = [
            'event_registration' => 'Inscriptions événements',
            'event_cancellation' => 'Annulations',
            'job_application'    => 'Candidatures',
            'auth_login'         => 'Connexions',
            'auth_register'      => 'Inscriptions membres',
        ];

        $colors = [
            'event_registration' => 'rgba(34,197,94,0.75)',
            'event_cancellation' => 'rgba(239,68,68,0.75)',
            'job_application'    => 'rgba(59,130,246,0.75)',
            'auth_login'         => 'rgba(249,115,22,0.75)',
            'auth_register'      => 'rgba(168,85,247,0.75)',
        ];

        $datasets = [];

        foreach ($rows as $type => $typeRows) {
            $byDay = $typeRows->pluck('total', 'day')->map(fn ($v) => (int) $v)->all();
            $data  = [];

            for ($date = $from->copy(); $date->lte($to); $date->addDay()) {
                $data[] = $byDay[$date->format('Y-m-d')] ?? 0;
            }

            $datasets[] = [
                'label'           => $typeLabels[$type] ?? $type,
                'data'            => $data,
                'backgroundColor' => $colors[$type] ?? 'rgba(100,116,139,0.75)',
                'borderRadius'    => 4,
            ];
        }

        return ['labels' => $labels, 'datasets' => $datasets];
    }

    /**
     * Device distribution for the period.
     *
     * @return array<string, int>
     */
    public function getDeviceBreakdown(string $period): array
    {
        [$from, $to] = $this->parsePeriod($period);

        return PageView::selectRaw('device_type, COUNT(*) as total')
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('device_type')
            ->pluck('total', 'device_type')
            ->map(fn ($v) => (int) $v)
            ->all();
    }

    private function detectDevice(string $ua): string
    {
        if (preg_match('/(tablet|ipad|playbook|silk)|(android(?!.*mobile))/i', $ua)) {
            return 'tablet';
        }

        if (preg_match('/(mobile|phone|android|iphone|ipod|blackberry|palm|opera mini|webos)/i', $ua)) {
            return 'mobile';
        }

        return 'desktop';
    }

    private function detectBrowser(string $ua): string
    {
        $ua = strtolower($ua);

        if (str_contains($ua, 'edg')) {
            return 'Edge';
        }

        if (str_contains($ua, 'chrome') && ! str_contains($ua, 'chromium')) {
            return 'Chrome';
        }

        if (str_contains($ua, 'firefox')) {
            return 'Firefox';
        }

        if (str_contains($ua, 'safari') && ! str_contains($ua, 'chrome')) {
            return 'Safari';
        }

        if (str_contains($ua, 'opera') || str_contains($ua, 'opr')) {
            return 'Opera';
        }

        return 'Other';
    }

    private function sanitizeUrl(?string $url): ?string
    {
        if ($url === null || $url === '') {
            return null;
        }

        return substr($url, 0, 500);
    }
}
