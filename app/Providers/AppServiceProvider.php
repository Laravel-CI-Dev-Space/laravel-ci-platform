<?php

namespace App\Providers;

use App\View\Composers\GlobalSettingsComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Injecte $globalSettings dans le layout web, le header et le footer.
        View::composer(
            ['layouts.web', 'components.web.header', 'components.web.footer'],
            GlobalSettingsComposer::class
        );
    }
}
