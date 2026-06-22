<?php

declare(strict_types=1);

namespace App\Filament\Resources\Chat\ChatMonitoringResource\Widgets;

use App\Models\Chat\ChatTokenBudget;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class ChatTokenTrendWidget extends ChartWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = 'Consommation de tokens — 14 derniers jours';

    protected ?string $description = 'Public vs Dashboard par jour';

    protected int|string|array $columnSpan = 'full';

    protected static ?string $maxHeight = '220px';

    public ?string $filter = '14d';

    protected function getFilters(): ?array
    {
        return [
            '7d'  => '7 derniers jours',
            '14d' => '14 derniers jours',
            '30d' => '30 derniers jours',
        ];
    }

    protected function getData(): array
    {
        $days = match ($this->filter) {
            '7d'  => 7,
            '30d' => 30,
            default => 14,
        };

        $labels    = [];
        $public    = [];
        $dashboard = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date     = Carbon::today()->subDays($i);
            $labels[] = $date->format('d/m');

            $row = ChatTokenBudget::whereDate('date', $date)
                ->selectRaw('SUM(public_tokens_used) as pub, SUM(dashboard_tokens_used) as dash')
                ->first();

            $public[]    = (int) ($row->pub ?? 0);
            $dashboard[] = (int) ($row->dash ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Public',
                    'data'            => $public,
                    'backgroundColor' => 'rgba(232,89,12,0.15)',
                    'borderColor'     => 'rgb(232,89,12)',
                    'fill'            => true,
                    'tension'         => 0.4,
                    'pointBackgroundColor' => 'rgb(232,89,12)',
                ],
                [
                    'label'           => 'Dashboard',
                    'data'            => $dashboard,
                    'backgroundColor' => 'rgba(74,108,247,0.12)',
                    'borderColor'     => 'rgb(74,108,247)',
                    'fill'            => true,
                    'tension'         => 0.4,
                    'pointBackgroundColor' => 'rgb(74,108,247)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['display' => true, 'position' => 'top'],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'grid' => ['display' => true, 'color' => 'rgba(0,0,0,0.05)'],
                    'ticks' => [
                        'callback' => "function(v){ return v >= 1000 ? (v/1000).toFixed(1)+'k' : v; }",
                    ],
                ],
                'x' => ['grid' => ['display' => false]],
            ],
            'interaction' => ['mode' => 'nearest', 'axis' => 'x', 'intersect' => false],
        ];
    }
}
