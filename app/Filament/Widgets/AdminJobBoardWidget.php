<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\UserRole;
use App\Models\JobOffer;
use Filament\Widgets\ChartWidget;

class AdminJobBoardWidget extends ChartWidget
{
    protected ?string $heading = "Répartition des offres d'emploi par statut";

    protected static ?int $sort = 6;

    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole([
            UserRole::SuperAdmin->value,
            UserRole::Admin->value,
        ]) ?? false;
    }

    protected function getData(): array
    {
        $statuses = ['active', 'pending', 'expired', 'filled', 'rejected'];
        $labels   = ['Actives', 'En attente', 'Expirées', 'Pourvues', 'Refusées'];
        $colors   = [
            'rgba(46,204,113,0.8)',
            'rgba(243,156,18,0.8)',
            'rgba(231,76,60,0.8)',
            'rgba(52,152,219,0.8)',
            'rgba(149,165,166,0.8)',
        ];

        $data = array_map(
            fn ($s) => JobOffer::where('status', $s)->count(),
            $statuses,
        );

        return [
            'datasets' => [
                [
                    'label'           => 'Offres',
                    'data'            => $data,
                    'backgroundColor' => $colors,
                    'borderWidth'     => 2,
                    'borderColor'     => '#fff',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['display' => true, 'position' => 'right'],
            ],
            'cutout' => '65%',
        ];
    }
}
