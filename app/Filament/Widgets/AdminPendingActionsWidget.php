<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\UserRole;
use App\Models\Article;
use App\Models\CompanyRegistrationRequest;
use App\Models\JobOffer;
use App\Models\Question;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminPendingActionsWidget extends BaseWidget
{
    protected ?string $heading = 'Actions en attente';

    protected static ?int $sort = 4;

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
        $user          = auth()->user();
        $isModerator   = $user?->hasRole(UserRole::Moderator->value)
            && ! $user?->hasAnyRole([UserRole::Admin->value, UserRole::SuperAdmin->value]);

        $pendingArticles  = Article::where('status', 'pending')->count();
        $rejectedArticles = Article::where('status', 'rejected')->count();
        $hiddenQuestions  = Question::where('status', 'hidden')->count();

        $stats = [
            Stat::make('Articles en attente de validation', $pendingArticles)
                ->description("{$rejectedArticles} rejetés cette semaine")
                ->descriptionIcon('heroicon-m-document-check')
                ->color($pendingArticles > 0 ? 'warning' : 'success')
                ->url($pendingArticles > 0 ? '/admin/articles?tableFilters[pending][isActive]=true' : null),

            Stat::make('Questions signalées / cachées', $hiddenQuestions)
                ->description('Questions masquées par la modération')
                ->descriptionIcon('heroicon-m-eye-slash')
                ->color($hiddenQuestions > 0 ? 'danger' : 'success'),
        ];

        if (! $isModerator) {
            $pendingOffers    = JobOffer::where('status', 'pending')->count();
            $pendingCompanies = CompanyRegistrationRequest::where('status', 'pending')->count();
            $rejectedOffers   = JobOffer::where('status', 'rejected')->count();

            $stats[] = Stat::make("Offres en attente", $pendingOffers)
                ->description("{$rejectedOffers} refusées · {$pendingCompanies} demandes entreprise en attente")
                ->descriptionIcon('heroicon-m-briefcase')
                ->color($pendingOffers > 0 ? 'warning' : 'success')
                ->url($pendingOffers > 0 ? '/admin/job-offers?tableFilters[pending][isActive]=true' : null);

            $stats[] = Stat::make('Demandes entreprise', $pendingCompanies)
                ->description('À valider pour accès au portail recruteur')
                ->descriptionIcon('heroicon-m-building-office')
                ->color($pendingCompanies > 0 ? 'danger' : 'success')
                ->url('/admin/companies/company-registrations');
        }

        return $stats;
    }
}
