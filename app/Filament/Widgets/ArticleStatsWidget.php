<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Article;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ArticleStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected static bool $isDiscovered = false;

    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $total      = Article::count();
        $published  = Article::where('status', 'published')->count();
        $pending    = Article::where('status', 'pending')->count();
        $rejected   = Article::where('status', 'rejected')->count();
        $draft      = Article::where('status', 'draft')->count();
        $totalViews = (int) Article::sum('views_count');

        $publishedThisMonth = Article::where('status', 'published')
            ->whereMonth('published_at', now()->month)
            ->whereYear('published_at', now()->year)
            ->count();

        return [
            Stat::make('Articles total', $total)
                ->description("{$published} publiés · {$draft} brouillons")
                ->color('primary')
                ->icon('heroicon-o-document-text'),

            Stat::make('En attente de validation', $pending)
                ->description($pending > 0 ? 'Action requise' : 'Aucun article en attente')
                ->color($pending > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-document-check'),

            Stat::make('Rejetés', $rejected)
                ->description('Articles refusés par la modération')
                ->color($rejected > 0 ? 'danger' : 'gray')
                ->icon('heroicon-o-x-circle'),

            Stat::make('Publiés ce mois', $publishedThisMonth)
                ->description(now()->translatedFormat('F Y'))
                ->color('success')
                ->icon('heroicon-o-check-circle'),

            Stat::make('Vues totales', number_format($totalViews))
                ->description('Cumulées sur tous les articles')
                ->color('info')
                ->icon('heroicon-o-eye'),
        ];
    }
}
