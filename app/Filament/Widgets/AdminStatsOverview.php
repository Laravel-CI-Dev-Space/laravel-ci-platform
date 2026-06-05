<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\UserRole;
use App\Models\Article;
use App\Models\CompanyRegistrationRequest;
use App\Models\JobOffer;
use App\Models\Question;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole([
            UserRole::SuperAdmin->value,
            UserRole::Admin->value,
            UserRole::Moderator->value,
        ]) ?? false;
    }

    protected function getStats(): array
    {
        $user = auth()->user();
        $isModerator = $user?->hasRole(UserRole::Moderator->value) && ! $user?->hasAnyRole([UserRole::Admin->value, UserRole::SuperAdmin->value]);

        // Membres
        $totalMembers   = User::count();
        $newThisMonth   = User::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
        $newLastMonth   = User::whereMonth('created_at', now()->subMonth()->month)->count();
        $memberTrend    = $newLastMonth > 0 ? round((($newThisMonth - $newLastMonth) / $newLastMonth) * 100) : 0;

        // Contenu
        $pendingArticles   = Article::where('status', 'pending')->count();
        $publishedArticles = Article::where('status', 'published')->count();
        $totalQuestions    = Question::where('status', 'published')->count();
        $answeredQuestions = Question::whereNotNull('accepted_answer_id')->count();

        $stats = [
            Stat::make('Membres actifs', number_format($totalMembers))
                ->description(($memberTrend >= 0 ? '+' : '') . $memberTrend . '% vs mois dernier · ' . $newThisMonth . ' ce mois')
                ->descriptionIcon($memberTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($memberTrend >= 0 ? 'success' : 'warning')
                ->chart(array_map(fn ($m) => User::whereMonth('created_at', $m)->count(), range(1, 12))),

            Stat::make('Articles publiés', number_format($publishedArticles))
                ->description("{$pendingArticles} en attente de validation")
                ->descriptionIcon('heroicon-m-document-text')
                ->color($pendingArticles > 0 ? 'warning' : 'success'),

            Stat::make('Questions forum', number_format($totalQuestions))
                ->description($totalQuestions > 0 ? round(($answeredQuestions / $totalQuestions) * 100) . '% résolues' : 'Aucune question')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('info'),
        ];

        // Stats supplémentaires pour admin/super-admin uniquement
        if (! $isModerator) {
            $activeOffers   = JobOffer::where('status', 'active')->count();
            $pendingOffers  = JobOffer::where('status', 'pending')->count();
            $pendingCompanies = CompanyRegistrationRequest::where('status', 'pending')->count();

            $stats[] = Stat::make("Offres d'emploi actives", number_format($activeOffers))
                ->description("{$pendingOffers} en attente · {$pendingCompanies} demandes entreprise")
                ->descriptionIcon('heroicon-m-briefcase')
                ->color($pendingOffers > 0 ? 'warning' : 'success');
        }

        return $stats;
    }
}
