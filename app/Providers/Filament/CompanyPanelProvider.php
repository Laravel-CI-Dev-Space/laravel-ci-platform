<?php

namespace App\Providers\Filament;

use App\Filament\Company\Widgets\CompanyOffersWidget;
use App\Filament\Company\Widgets\CompanyRecentApplicationsWidget;
use App\Filament\Company\Widgets\CompanyStatsOverview;
use App\Http\Middleware\FilamentCompanyAccess;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class CompanyPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('company')
            ->path('company/portal')
            ->authGuard('company')

            ->colors([
                'primary' => Color::Orange,
                'gray'    => Color::Slate,
            ])

            ->brandName('Espace Entreprise - Laravel CI')
            ->brandLogo(asset('assets/logo.jpeg'))
            ->brandLogoHeight('2rem')
            ->favicon(asset('assets/logo.jpeg'))

            // Auth gérée par notre flow custom (/company/login)
            ->login(false)

            ->discoverResources(
                in: app_path('Filament/Company/Resources'),
                for: 'App\Filament\Company\Resources'
            )
            ->discoverWidgets(
                in: app_path('Filament/Company/Widgets'),
                for: 'App\Filament\Company\Widgets'
            )

            ->pages([Dashboard::class])
            ->widgets([
                CompanyStatsOverview::class,
                CompanyRecentApplicationsWidget::class,
                CompanyOffersWidget::class,
                AccountWidget::class,
            ])

            ->navigationGroups([
                'Recrutement',
                'Mon compte',
            ])

            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                FilamentCompanyAccess::class,
            ]);
    }
}
