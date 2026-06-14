<?php

use App\Http\Middleware\CheckMemberActive;
use App\Http\Middleware\CompanyActive;
use App\Http\Middleware\CompanyAuthenticated;
use App\Http\Middleware\CompanyGuest;
use App\Http\Middleware\CompanyMustChangePassword;
use App\Http\Middleware\EnsureProfileComplete;
use App\Http\Middleware\RunScheduler;
use App\Http\Middleware\TrackPageView;
use App\Http\Middleware\ViewingAsMember;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            TrackPageView::class,
            RunScheduler::class,
            ViewingAsMember::class,
        ]);

        $middleware->alias([
            // Middleware custom
            'active'            => CheckMemberActive::class,
            'profile.complete'  => EnsureProfileComplete::class,
            'company.auth'      => CompanyAuthenticated::class,
            'company.active'    => CompanyActive::class,
            'company.password'  => CompanyMustChangePassword::class,
            'company.guest'     => CompanyGuest::class,
            'viewing.as.member' => ViewingAsMember::class,

            // Middlewares Spatie Permission
            'role'               => RoleMiddleware::class,
            'permission'         => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
