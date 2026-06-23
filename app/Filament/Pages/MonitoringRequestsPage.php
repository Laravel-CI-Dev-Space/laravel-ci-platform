<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Enums\UserRole;
use App\Models\Analytics\PageView;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class MonitoringRequestsPage extends Page
{
    protected static BackedEnum|string|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $navigationLabel = 'Détail requêtes';

    protected static ?string $title = 'Détail des requêtes';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.monitoring-requests';

    /** slow | errors | routes */
    public string $filter = 'slow';

    public static function getNavigationGroup(): ?string
    {
        return 'Monitoring';
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole([
            UserRole::SuperAdmin->value,
            UserRole::Admin->value,
        ]) ?? false;
    }

    public function mount(): void
    {
        $this->filter = request()->get('filter', 'slow');
    }

    public function setFilter(string $filter): void
    {
        $this->filter = $filter;
    }

    public function getRowsProperty(): \Illuminate\Support\Collection
    {
        $since = now()->subHours(24);

        return match ($this->filter) {
            'errors' => PageView::whereNotNull('status_code')
                ->where('status_code', '>=', 400)
                ->where('created_at', '>=', $since)
                ->orderByDesc('created_at')
                ->limit(300)
                ->get(['method', 'path', 'status_code', 'duration_ms', 'route_name', 'user_id', 'created_at']),

            'routes' => PageView::whereNotNull('duration_ms')
                ->where('created_at', '>=', $since)
                ->selectRaw('COALESCE(route_name, path) as label, COUNT(*) as hits, ROUND(AVG(duration_ms)) as avg_ms, MAX(duration_ms) as max_ms, SUM(CASE WHEN status_code >= 400 THEN 1 ELSE 0 END) as errors')
                ->groupBy('route_name', 'path')
                ->orderByDesc('avg_ms')
                ->limit(100)
                ->get(),

            default => PageView::whereNotNull('duration_ms')
                ->where('duration_ms', '>', 500)
                ->where('created_at', '>=', $since)
                ->orderByDesc('duration_ms')
                ->limit(300)
                ->get(['method', 'path', 'status_code', 'duration_ms', 'route_name', 'user_id', 'created_at']),
        };
    }
}
