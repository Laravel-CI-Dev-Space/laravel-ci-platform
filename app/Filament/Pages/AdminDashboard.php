<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\Article;
use App\Models\CompanyRegistrationRequest;
use App\Models\JobOffer;
use App\Models\Question;
use App\Models\User;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class AdminDashboard extends Page
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-home';

    protected string $view = 'filament.pages.admin-dashboard';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?int $navigationSort = -2;

    /** Définit l'URL de la page comme / (root du panel). */
    public static function getRoutePath(\Filament\Panel $panel): string
    {
        return '/';
    }

    public static function getNavigationGroup(): ?string
    {
        return null;
    }

    public function getTitle(): string
    {
        return 'Dashboard';
    }

    protected function getViewData(): array
    {
        // ── KPI principale ─────────────────────────────────
        $totalMembers      = User::count();
        $membersThisMonth  = User::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
        $membersLastMonth  = User::whereMonth('created_at', now()->subMonth()->month)->whereYear('created_at', now()->subMonth()->year)->count();
        $memberTrend       = $membersLastMonth > 0 ? round((($membersThisMonth - $membersLastMonth) / $membersLastMonth) * 100) : 0;

        $publishedArticles = Article::where('status', 'published')->count();
        $pendingArticles   = Article::where('status', 'pending')->count();
        $articlesThisMonth = Article::where('status', 'published')->whereMonth('created_at', now()->month)->count();

        $totalQuestions    = Question::where('status', 'published')->count();
        $answeredCount     = Question::whereNotNull('accepted_answer_id')->count();
        $answeredPct       = $totalQuestions > 0 ? round(($answeredCount / $totalQuestions) * 100) : 0;

        $activeOffers      = JobOffer::where('status', 'active')->count();
        $pendingOffers     = JobOffer::where('status', 'pending')->count();
        $totalApplications = \App\Models\JobApplication::count();

        // ── Chart contenu (6 mois) ─────────────────────────
        $chartLabels = [];
        $chartArticles = $chartQuestions = $chartOffers = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = Carbon::now()->subMonths($i);
            $chartLabels[]   = $m->translatedFormat('M');
            $chartArticles[] = Article::whereYear('created_at', $m->year)->whereMonth('created_at', $m->month)->count();
            $chartQuestions[]= Question::whereYear('created_at', $m->year)->whereMonth('created_at', $m->month)->count();
            $chartOffers[]   = JobOffer::whereYear('created_at', $m->year)->whereMonth('created_at', $m->month)->count();
        }

        // ── Chart croissance membres (12 mois) ─────────────
        $growthLabels = $growthData = [];
        for ($i = 11; $i >= 0; $i--) {
            $m = Carbon::now()->subMonths($i);
            $growthLabels[] = $m->translatedFormat('M');
            $growthData[]   = User::whereYear('created_at', $m->year)->whereMonth('created_at', $m->month)->count();
        }

        // ── Membres récents ────────────────────────────────
        $recentMembers = User::with('roles')->latest()->take(7)->get();

        // ── Actions en attente ─────────────────────────────
        $pendingCompanies = CompanyRegistrationRequest::where('status', 'pending')->count();
        $rejectedArticles = Article::where('status', 'rejected')->count();
        $hiddenQuestions  = Question::where('status', 'hidden')->count();

        // ── Répartition offres ─────────────────────────────
        $offersActive   = JobOffer::where('status', 'active')->count();
        $offersExpired  = JobOffer::where('status', 'expired')->count();
        $offersFilled   = JobOffer::where('status', 'filled')->count();
        $offersPending  = JobOffer::where('status', 'pending')->count();
        $offersTotal    = max($offersActive + $offersExpired + $offersFilled + $offersPending, 1);

        return compact(
            'totalMembers', 'membersThisMonth', 'memberTrend',
            'publishedArticles', 'pendingArticles', 'articlesThisMonth',
            'totalQuestions', 'answeredPct', 'hiddenQuestions',
            'activeOffers', 'pendingOffers', 'totalApplications',
            'chartLabels', 'chartArticles', 'chartQuestions', 'chartOffers',
            'growthLabels', 'growthData',
            'recentMembers',
            'pendingArticles', 'pendingOffers', 'pendingCompanies', 'rejectedArticles',
            'offersActive', 'offersExpired', 'offersFilled', 'offersPending', 'offersTotal',
        );
    }
}
