<?php

namespace App\Providers\Filament;

use App\Filament\Pages\AdminDashboard;
use App\Filament\Resources\Newsletter\NewsletterSubscriberResource;
use App\Http\Middleware\FilamentAdminAccess;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Vite;
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

            ->brandName('Laravel CI — Admin')
            ->brandLogo(asset('assets/logo.jpeg'))
            ->brandLogoHeight('2rem')
            ->favicon(asset('assets/logo.jpeg'))

            // No Filament login page — authentication is handled via GitHub OAuth
            ->login(false)
            ->authGuard('web')

            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->resources([NewsletterSubscriberResource::class])
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')

            ->pages([AdminDashboard::class])
            ->widgets([AccountWidget::class])

            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />' .
                    app(Vite::class)(['resources/css/app.css'])->toHtml()
            )

            ->renderHook(
                PanelsRenderHook::TOPBAR_END,
                fn (): string => view('filament.notification-bell-hook')->render(),
            )

            ->navigationGroups([
                NavigationGroup::make('Entreprises'),
                NavigationGroup::make('Job Board'),
                NavigationGroup::make('Membres'),
                NavigationGroup::make('Communauté'),
                NavigationGroup::make('Contenu'),
                NavigationGroup::make('Monitoring'),
                NavigationGroup::make('Configuration')->collapsed(),
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
                FilamentAdminAccess::class, // enforces super-admin or admin role
            ]);
    }
}
