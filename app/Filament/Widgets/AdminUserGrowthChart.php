<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\UserRole;
use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class AdminUserGrowthChart extends ChartWidget
{
    protected ?string $heading = 'Croissance des membres - 12 derniers mois';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole([
            UserRole::SuperAdmin->value,
            UserRole::SuperAdmin->value,
        ]) ?? false;
    }

    protected function getData(): array
    {
        $labels        = [];
        $newUsers      = [];
        $cumulUsers    = [];
        $cumul         = 0;

        for ($i = 11; $i >= 0; $i--) {
            $month          = Carbon::now()->subMonths($i);
            $labels[]       = $month->translatedFormat('M Y');
            $count          = User::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            $newUsers[]     = $count;
            $cumul         += $count;
            $cumulUsers[]   = $cumul;
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Nouveaux membres',
                    'data'            => $newUsers,
                    'backgroundColor' => 'rgba(232,89,12,0.15)',
                    'borderColor'     => 'rgb(232,89,12)',
                    'fill'            => true,
                    'tension'         => 0.4,
                ],
                [
                    'label'           => 'Total cumulé',
                    'data'            => $cumulUsers,
                    'backgroundColor' => 'rgba(74,108,247,0.1)',
                    'borderColor'     => 'rgb(74,108,247)',
                    'fill'            => false,
                    'tension'         => 0.4,
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
            ],
            'scales' => [
                'y' => ['beginAtZero' => true, 'grid' => ['display' => true]],
                'x' => ['grid' => ['display' => false]],
            ],
        ];
    }
}
