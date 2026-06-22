<?php

declare(strict_types=1);

namespace App\Filament\Resources\Chat\ChatMonitoringResource\Widgets;

use App\Models\Chat\ChatTokenBudget;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ChatTopUsersWidget extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Top utilisateurs — 30 derniers jours';

    protected ?string $description = 'Tokens consommés par utilisateur';

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $since = Carbon::now()->subDays(30)->startOfDay();

        $rows = ChatTokenBudget::where('date', '>=', $since)
            ->selectRaw('user_id, SUM(public_tokens_used + dashboard_tokens_used) as total')
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->limit(10)
            ->with('user:id,name')
            ->get();

        $labels  = $rows->map(fn ($r) => $r->user?->name ?? "User #{$r->user_id}")->toArray();
        $totals  = $rows->map(fn ($r) => (int) $r->total)->toArray();

        $colors = [
            'rgba(232,89,12,0.8)',
            'rgba(74,108,247,0.8)',
            'rgba(16,185,129,0.8)',
            'rgba(245,158,11,0.8)',
            'rgba(139,92,246,0.8)',
            'rgba(236,72,153,0.8)',
            'rgba(20,184,166,0.8)',
            'rgba(239,68,68,0.8)',
            'rgba(59,130,246,0.8)',
            'rgba(107,114,128,0.8)',
        ];

        return [
            'datasets' => [
                [
                    'label'           => 'Tokens consommés',
                    'data'            => $totals,
                    'backgroundColor' => array_slice($colors, 0, count($totals)),
                    'borderRadius'    => 6,
                    'borderSkipped'   => false,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['display' => false],
                'tooltip' => [
                    'callbacks' => [
                        'label' => "function(ctx){ var v=ctx.raw; return v>=1000?(v/1000).toFixed(1)+'k tokens':v+' tokens'; }",
                    ],
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'grid' => ['display' => true, 'color' => 'rgba(0,0,0,0.05)'],
                    'ticks' => [
                        'callback' => "function(v){ return v>=1000?(v/1000).toFixed(1)+'k':v; }",
                    ],
                ],
                'x' => ['grid' => ['display' => false]],
            ],
            'indexAxis' => 'y',
        ];
    }
}
