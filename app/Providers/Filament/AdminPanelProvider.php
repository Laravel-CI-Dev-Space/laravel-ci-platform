<?php

namespace App\Providers\Filament;

use App\Filament\Pages\AdminDashboard;
use App\Filament\Resources\Events\Pages\ListEvents;
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
                fn (): string =>
                    '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />' .
                    '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" />' .
                    '<style>[x-cloak]{display:none!important}</style>' .
                    app(Vite::class)(['resources/css/app.css'])->toHtml()
            )

            ->renderHook(
                PanelsRenderHook::TOPBAR_END,
                fn (): string => view('filament.notification-bell-hook')->render(),
            )

            ->renderHook(
                PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_BEFORE,
                function (): string {
                    $mapEvent = fn ($e) => [
                        'id'         => $e->id,
                        'title'      => $e->title,
                        'type'       => $e->type->value ?? 'meetup',
                        'status'     => $e->status->value ?? 'draft',
                        'starts_at'  => $e->starts_at->format('H:i'),
                        'ends_at'    => $e->ends_at?->format('H:i'),
                        'location'   => $e->location,
                        'is_paid'    => (bool) $e->is_paid,
                        'price'      => $e->price,
                        'currency'   => $e->currency,
                        'capacity'   => $e->capacity,
                        'registered' => $e->registrations->count(),
                        'edit_url'   => \App\Filament\Resources\Events\EventResource::getUrl('edit', ['record' => $e]),
                        'view_url'   => route('events.show', $e->slug),
                    ];

                    // Load 3 months: prev, current, next — indexed by 'YYYY-MM-DD'
                    $now    = now();
                    $start  = $now->copy()->subMonth()->startOfMonth();
                    $end    = $now->copy()->addMonth()->endOfMonth();

                    $events = \App\Models\Event::whereBetween('starts_at', [$start, $end])
                        ->with('registrations')
                        ->get();

                    $byDate = [];
                    foreach ($events as $e) {
                        $key = $e->starts_at->format('Y-m-d');
                        $byDate[$key][] = $mapEvent($e);
                    }

                    return view('filament.partials.event-calendar', [
                        'initMonth' => (int) $now->month,
                        'initYear'  => (int) $now->year,
                        'byDate'    => $byDate,
                        'today'     => $now->format('Y-m-d'),
                    ])->render();
                },
                scopes: ListEvents::class,
            )

            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): \Illuminate\Contracts\View\View => view('filament.partials.chat-widget'),
            )

            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => <<<'HTML'
                <script>
                (function () {
                    function splitBadges() {
                        document.querySelectorAll('.fi-badge').forEach(function (badge) {
                            if (badge.dataset.badgeSplit) return;
                            var text = badge.textContent.trim();
                            var m = text.match(/^(\d+)\s*\+(\d+)$/);
                            if (!m) return;
                            badge.dataset.badgeSplit = '1';
                            badge.style.cssText = 'background:transparent!important;padding:0!important;display:inline-flex;gap:3px;align-items:center;';
                            badge.innerHTML =
                                '<span style="background:rgba(255,255,255,0.12);color:inherit;border-radius:9999px;padding:1px 7px;font-size:inherit;font-weight:inherit;">' + m[1] + '</span>' +
                                '<span style="background:#e8580a;color:#fff;border-radius:9999px;padding:1px 7px;font-size:inherit;font-weight:inherit;">+' + m[2] + '</span>';
                        });
                    }
                    document.addEventListener('DOMContentLoaded', splitBadges);
                    new MutationObserver(splitBadges).observe(document.documentElement, { childList: true, subtree: true });
                })();
                </script>
                HTML
            )

            ->navigationGroups([
                NavigationGroup::make('Forum'),
                NavigationGroup::make('Blog'),
                NavigationGroup::make('Événements'),
                NavigationGroup::make('Job Board'),
                NavigationGroup::make('Recruteurs'),
                NavigationGroup::make('Membres'),
                NavigationGroup::make('Communication'),
                NavigationGroup::make('Vitrine')->collapsed(),
                NavigationGroup::make('Monitoring')->collapsed(),
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
