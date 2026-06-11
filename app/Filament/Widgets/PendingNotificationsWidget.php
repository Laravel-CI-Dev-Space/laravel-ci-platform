<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\UserRole;
use App\Filament\Resources\Companies\CompanyRegistrationResource;
use App\Models\Article;
use App\Models\CompanyRegistrationRequest;
use App\Models\Report;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PendingNotificationsWidget extends BaseWidget
{
    protected ?string $heading = 'Notifications en attente';

    protected static ?int $sort = 5;

    protected ?string $pollingInterval = '30s';

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
        $pendingReports   = Report::where('status', 'pending')->count();
        $pendingArticles  = Article::where('status', 'pending')->count();
        $pendingCompanies = CompanyRegistrationRequest::where('status', 'pending')->count();

        return [
            Stat::make('Signalements en attente', $pendingReports)
                ->description('À traiter par la modération')
                ->descriptionIcon('heroicon-m-flag')
                ->color($pendingReports > 0 ? 'danger' : 'success')
                ->url(route('dashboard.moderator.reports')),

            Stat::make('Articles en attente', $pendingArticles)
                ->description('À valider avant publication')
                ->descriptionIcon('heroicon-m-document-check')
                ->color($pendingArticles > 0 ? 'warning' : 'success')
                ->url('/admin/articles?tableFilters[pending][isActive]=true'),

            Stat::make('Demandes entreprise en attente', $pendingCompanies)
                ->description('À valider pour accès au portail recruteur')
                ->descriptionIcon('heroicon-m-building-office')
                ->color($pendingCompanies > 0 ? 'danger' : 'success')
                ->url(CompanyRegistrationResource::getUrl()),
        ];
    }
}
