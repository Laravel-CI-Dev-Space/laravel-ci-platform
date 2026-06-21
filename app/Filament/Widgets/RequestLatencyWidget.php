<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\UserRole;
use App\Models\Analytics\PageView;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RequestLatencyWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected static ?string $heading = 'Latence des requêtes — Top 20 routes les plus lentes (24h)';

    protected int | string | array $columnSpan = 'full';

    protected ?string $pollingInterval = '60s';

    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole([
            UserRole::SuperAdmin->value,
            UserRole::Admin->value,
        ]) ?? false;
    }

    public function getHeaderWidgetsData(): array
    {
        return Cache::remember('request_latency_header', 30, function (): array {
            $since = now()->subHours(24);

            $base = PageView::whereNotNull('duration_ms')
                ->where('created_at', '>=', $since);

            $avg   = (int) round((float) ((clone $base)->avg('duration_ms') ?? 0));
            $max   = (int) ((clone $base)->max('duration_ms') ?? 0);
            $total = (clone $base)->count();

            // P95 — order by duration_ms, skip to 95th percentile row
            $p95Index = max(0, (int) ceil($total * 0.95) - 1);
            $p95      = (int) ((clone $base)
                ->orderBy('duration_ms')
                ->skip($p95Index)
                ->value('duration_ms') ?? 0);

            $slow = (clone $base)->where('duration_ms', '>', 500)->count();

            return compact('avg', 'max', 'p95', 'total', 'slow');
        });
    }

    protected function getTableQuery(): Builder
    {
        $since = now()->subHours(24);

        return PageView::query()
            ->select(
                DB::raw('MD5(CONCAT(COALESCE(route_name,""), path, method)) as id'),
                DB::raw('COALESCE(route_name, path) as route_label'),
                DB::raw('route_name'),
                DB::raw('path'),
                DB::raw('method'),
                DB::raw('COUNT(*) as hits'),
                DB::raw('ROUND(AVG(duration_ms)) as avg_ms'),
                DB::raw('MAX(duration_ms) as max_ms'),
                DB::raw('MIN(duration_ms) as min_ms'),
                DB::raw('SUM(CASE WHEN duration_ms > 500 THEN 1 ELSE 0 END) as slow_hits'),
                DB::raw('SUM(CASE WHEN status_code >= 400 THEN 1 ELSE 0 END) as error_hits'),
            )
            ->whereNotNull('duration_ms')
            ->where('created_at', '>=', $since)
            ->groupBy('route_name', 'path', 'method')
            ->orderByDesc('avg_ms');
    }

    public function getTableRecordKey(\Illuminate\Database\Eloquent\Model|array $record): string
    {
        $r = is_array($record) ? (object) $record : $record;

        return (string) ($r->id ?? md5(($r->path ?? '') . ($r->method ?? '')));
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('method')
                ->label('Méthode')
                ->badge()
                ->color(fn ($state) => match ($state) {
                    'GET'    => 'info',
                    'POST'   => 'success',
                    'PUT',
                    'PATCH'  => 'warning',
                    'DELETE' => 'danger',
                    default  => 'gray',
                })
                ->width('80px'),

            TextColumn::make('route_label')
                ->label('Route / Chemin')
                ->description(fn ($record) => $record->route_name ? $record->path : null)
                ->searchable(query: function (Builder $query, string $search): Builder {
                    return $query->where(function ($q) use ($search): void {
                        $q->where('route_name', 'like', "%{$search}%")
                          ->orWhere('path', 'like', "%{$search}%");
                    });
                })
                ->wrap(),

            TextColumn::make('hits')
                ->label('Requêtes')
                ->numeric()
                ->sortable()
                ->alignCenter(),

            TextColumn::make('avg_ms')
                ->label('Moy.')
                ->suffix(' ms')
                ->sortable()
                ->badge()
                ->color(fn ($state) => match (true) {
                    $state <= 150  => 'success',
                    $state <= 500  => 'warning',
                    default        => 'danger',
                })
                ->alignCenter(),

            TextColumn::make('p95_display')
                ->label('P95')
                ->getStateUsing(fn ($record) => '—')
                ->alignCenter()
                ->color('gray'),

            TextColumn::make('max_ms')
                ->label('Max')
                ->suffix(' ms')
                ->sortable()
                ->color(fn ($state) => $state > 1000 ? 'danger' : 'gray')
                ->alignCenter(),

            TextColumn::make('min_ms')
                ->label('Min')
                ->suffix(' ms')
                ->alignCenter()
                ->color('gray'),

            TextColumn::make('slow_hits')
                ->label('Lentes >500ms')
                ->badge()
                ->color(fn ($state) => $state > 0 ? 'warning' : 'success')
                ->alignCenter(),

            TextColumn::make('error_hits')
                ->label('Erreurs 4xx/5xx')
                ->badge()
                ->color(fn ($state) => $state > 0 ? 'danger' : 'success')
                ->alignCenter(),
        ];
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [20, 50];
    }

    protected function getTableDefaultSortColumn(): ?string
    {
        return 'avg_ms';
    }

    protected function getTableDefaultSortDirection(): ?string
    {
        return 'desc';
    }

    public function getTableHeading(): string|\Illuminate\Contracts\Support\Htmlable|null
    {
        $data = $this->getHeaderWidgetsData();

        return null;
    }

    protected function getFooterWidgets(): array
    {
        return [];
    }

    protected function getTableContentGrid(): ?array
    {
        return null;
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'Aucune donnée de latence';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return 'Les métriques apparaîtront après les premières requêtes enregistrées.';
    }
}
