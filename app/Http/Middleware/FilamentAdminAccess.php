<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FilamentAdminAccess
{
    /**
     * Only super-admin and admin roles may access the Filament panel.
     * Other authenticated users are redirected to their dashboard.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && ! auth()->user()->hasAnyRole([
            UserRole::SuperAdmin->value,
            UserRole::Admin->value,
        ])) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
