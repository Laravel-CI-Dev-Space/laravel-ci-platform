<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\UserRole;
use App\Models\Article;
use App\Models\JobOffer;
use App\Models\Question;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class AdminContentChart extends ChartWidget
{
    protected ?string $heading = 'Activité du contenu — 6 derniers mois';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole([
            UserRole::SuperAdmin->value,
            UserRole::Admin->value,
            UserRole::Moderator->value,
        ]) ?? false;
    }

    protected function getData(): array
    {
        $labels    = [];
        $articles  = [];
        $questions = [];
        $jobs      = [];

        for ($i = 5; $i >= 0; $i--) {
            $month       = Carbon::now()->subMonths($i);
            $labels[]    = $month->translatedFormat('M Y');
            $articles[]  = Article::whereYear('created_at', $month->year)->whereMonth('created_at', $month->month)->count();
            $questions[] = Question::whereYear('created_at', $month->year)->whereMonth('created_at', $month->month)->count();
            $jobs[]      = JobOffer::whereYear('created_at', $month->year)->whereMonth('created_at', $month->month)->count();
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Articles',
                    'data'            => $articles,
                    'backgroundColor' => 'rgba(232,89,12,0.75)',
                    'borderRadius'    => 6,
                ],
                [
                    'label'           => 'Questions forum',
                    'data'            => $questions,
                    'backgroundColor' => 'rgba(74,108,247,0.75)',
                    'borderRadius'    => 6,
                ],
                [
                    'label'           => "Offres d'emploi",
                    'data'            => $jobs,
                    'backgroundColor' => 'rgba(46,204,113,0.75)',
                    'borderRadius'    => 6,
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
                'legend' => ['display' => true, 'position' => 'top'],
            ],
            'scales' => [
                'y' => ['beginAtZero' => true, 'grid' => ['display' => true]],
                'x' => ['grid' => ['display' => false]],
            ],
        ];
    }
}
