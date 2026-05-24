<?php

namespace App\Http\Middleware;

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
        if (
            auth()->check() && ! auth()->user()->hasAnyRole(['super-admin', 'admin'])
        ) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
