<?php

namespace App\Providers\Filament;

use App\Http\Middleware\FilamentAdminAccess;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
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

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')

            ->colors([
                'primary' => Color::Orange,
                'gray'    => Color::Slate,
            ])

            ->brandName('🐘 Laravel CI — Admin')
            ->brandLogo(asset('assets/logo.jpeg'))
            ->brandLogoHeight('2rem')
            ->favicon(asset('assets/logo.jpeg'))

            // Pas de login Filament — on utilise notre auth GitHub
            ->login(false)
            ->authGuard('web')

            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')

            ->pages([Dashboard::class])
            ->widgets([AccountWidget::class])

            // ->navigationGroups([
            //     NavigationGroup::make('Membres')->icon('heroicon-o-users'),
            //     NavigationGroup::make('Contenu')->icon('heroicon-o-document-text'),
            //     NavigationGroup::make('Communauté')->icon('heroicon-o-calendar'),
            //     NavigationGroup::make('Configuration')->icon('heroicon-o-cog-6-tooth')->collapsed(),
            // ])

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
                Authenticate::class,
                FilamentAdminAccess::class,  // ← vérifie le rôle
            ]);
    }
}
