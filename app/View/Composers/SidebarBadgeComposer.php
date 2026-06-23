<?php

declare(strict_types=1);

namespace App\View\Composers;

use App\Services\SidebarBadgeService;
use Illuminate\View\View;

class SidebarBadgeComposer
{
    public function __construct(private readonly SidebarBadgeService $service) {}

    public function compose(View $view): void
    {
        if (! auth()->check()) {
            $view->with('sidebarBadges', []);
            return;
        }

        $user         = auth()->user();
        $currentRoute = request()->route()?->getName() ?? '';

        $view->with('sidebarBadges', $this->service->memberCounts($user, $currentRoute));
    }
}
