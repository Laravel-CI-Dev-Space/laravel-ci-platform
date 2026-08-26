<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
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

class PolePanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('espace-pole')
            ->path('espace-pole')

            ->colors([
                'primary' => Color::Orange,
                'gray'    => Color::Slate,
            ])

            ->brandName('Espace Gestionnaire — Laravel CI')
            ->brandLogo(asset('assets/web/img/logo.png'))
            ->brandLogoHeight('2rem')
            ->favicon(asset('assets/web/img/logo.png'))

            // Pas de login Filament - auth via GitHub OAuth (même flow que le panel admin)
            ->login(false)
            ->authGuard('web')

            ->discoverResources(
                in: app_path('Filament/Pole/Resources'),
                for: 'App\Filament\Pole\Resources'
            )

            ->pages([Dashboard::class])
            ->widgets([AccountWidget::class])

            ->navigationGroups([
                'Contenu',
                'Mon pôle',
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
                Authenticate::class,
            ]);
    }
}
