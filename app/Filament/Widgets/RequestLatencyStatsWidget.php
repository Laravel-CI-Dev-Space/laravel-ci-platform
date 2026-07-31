<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\UserRole;
use App\Models\Analytics\PageView;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class RequestLatencyStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected ?string $pollingInterval = '60s';

    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole([
            UserRole::SuperAdmin->value,
            UserRole::SuperAdmin->value,
        ]) ?? false;
    }

    protected function getStats(): array
    {
        [$avg24h, $p95_24h, $max24h, $total24h, $slow24h, $errorRate, $topSlowLabel, $topSlowAvg] = Cache::remember(
            'latency_stats_widget',
            now()->addSeconds(30),
            function (): array {
                $since = now()->subHours(24);
                $base  = PageView::whereNotNull('duration_ms')->where('created_at', '>=', $since);

                $total = (clone $base)->count();
                $avg   = (int) round((float) ((clone $base)->avg('duration_ms') ?? 0));
                $max   = (int) ((clone $base)->max('duration_ms') ?? 0);
                $slow  = (clone $base)->where('duration_ms', '>', 500)->count();
                $errors = (clone $base)->where('status_code', '>=', 400)->count();

                $errorRate = $total > 0 ? round(($errors / $total) * 100, 1) : 0.0;

                // P95
                $p95 = 0;
                if ($total > 0) {
                    $p95Index = max(0, (int) ceil($total * 0.95) - 1);
                    $p95      = (int) ((clone $base)
                        ->orderBy('duration_ms')
                        ->skip($p95Index)
                        ->value('duration_ms') ?? 0);
                }

                // Route la plus lente (avg) - on extrait les scalaires pour éviter
                // de sérialiser un objet Eloquent dans le cache
                $topSlowRow = (clone $base)
                    ->selectRaw('COALESCE(route_name, path) as label, ROUND(AVG(duration_ms)) as avg_ms')
                    ->groupBy('route_name', 'path')
                    ->orderByDesc('avg_ms')
                    ->first();

                $topSlowLabel = $topSlowRow?->label;
                $topSlowAvg   = $topSlowRow ? (int) $topSlowRow->avg_ms : null;

                return [$avg, $p95, $max, $total, $slow, $errorRate, $topSlowLabel, $topSlowAvg];
            }
        );

        $slowPct = $total24h > 0 ? round(($slow24h / $total24h) * 100, 1) : 0;

        return [
            Stat::make('Latence moyenne (24h)', $avg24h . ' ms')
                ->description($total24h . ' requête(s) enregistrée(s)')
                ->descriptionIcon('heroicon-m-clock')
                ->color(match (true) {
                    $avg24h <= 150  => 'success',
                    $avg24h <= 500  => 'warning',
                    default         => 'danger',
                }),

            Stat::make('Latence P95 (24h)', $p95_24h . ' ms')
                ->description('95% des requêtes sous ce seuil')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color(match (true) {
                    $p95_24h <= 300  => 'success',
                    $p95_24h <= 800  => 'warning',
                    default          => 'danger',
                })
                ->extraAttributes(['wire:click' => "\$dispatch('monitoring-open', { type: 'slow' })", 'style' => 'cursor:pointer']),

            Stat::make('Max (24h)', $max24h . ' ms')
                ->description('Requête la plus lente')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color($max24h > 2000 ? 'danger' : ($max24h > 1000 ? 'warning' : 'success'))
                ->extraAttributes(['wire:click' => "\$dispatch('monitoring-open', { type: 'slow' })", 'style' => 'cursor:pointer']),

            Stat::make('Requêtes lentes >500ms', $slow24h)
                ->description($slowPct . '% du trafic total')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($slowPct > 10 ? 'danger' : ($slowPct > 5 ? 'warning' : 'success'))
                ->extraAttributes(['wire:click' => "\$dispatch('monitoring-open', { type: 'slow' })", 'style' => 'cursor:pointer']),

            Stat::make('Taux d\'erreur (4xx/5xx)', $errorRate . '%')
                ->description('Sur les 24 dernières heures')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color($errorRate > 5 ? 'danger' : ($errorRate > 1 ? 'warning' : 'success'))
                ->extraAttributes(['wire:click' => "\$dispatch('monitoring-open', { type: 'errors' })", 'style' => 'cursor:pointer']),

            Stat::make('Route la plus lente', $topSlowAvg !== null ? $topSlowAvg . ' ms' : '-')
                ->description($topSlowLabel ? substr($topSlowLabel, 0, 40) : 'Pas de données')
                ->descriptionIcon('heroicon-m-map-pin')
                ->color('gray')
                ->extraAttributes(['wire:click' => "\$dispatch('monitoring-open', { type: 'routes' })", 'style' => 'cursor:pointer']),
        ];
    }
}
