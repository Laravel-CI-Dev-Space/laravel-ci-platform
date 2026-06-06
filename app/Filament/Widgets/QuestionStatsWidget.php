<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Question;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class QuestionStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected static bool $isDiscovered = false;

    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $total      = Question::count();
        $published  = Question::where('status', 'published')->count();
        $hidden     = Question::where('status', 'hidden')->count();
        $resolved   = Question::whereNotNull('accepted_answer_id')->count();
        $unanswered = Question::where('status', 'published')
            ->where('answers_count', 0)
            ->count();
        $resolvedPct = $published > 0 ? round(($resolved / $published) * 100) : 0;
        $pinned      = Question::where('is_pinned', true)->count();

        return [
            Stat::make('Questions total', number_format($total))
                ->description("{$published} publiées · {$hidden} cachées")
                ->color('primary')
                ->icon('heroicon-o-chat-bubble-left-right'),

            Stat::make('Résolues', $resolved)
                ->description("{$resolvedPct}% du total publié")
                ->color('success')
                ->icon('heroicon-o-check-circle'),

            Stat::make('Sans réponse', $unanswered)
                ->description('Questions publiées sans aucune réponse')
                ->color($unanswered > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-question-mark-circle'),

            Stat::make('Cachées / modérées', $hidden)
                ->description('Nécessitent une action de modération')
                ->color($hidden > 0 ? 'danger' : 'gray')
                ->icon('heroicon-o-eye-slash'),

            Stat::make('Épinglées', $pinned)
                ->description('Mises en avant sur le forum')
                ->color('info')
                ->icon('heroicon-o-map-pin'),
        ];
    }
}
