<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Remplace le middleware natif guest:company.
 * Laravel redirige guest:X vers RouteServiceProvider::HOME,
 * ce qui pointe vers /dashboard (web) → boucle sur le login web.
 * Ce middleware redirige correctement vers company.dashboard.
 */
class CompanyGuest
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth('company')->check()) {
            return redirect()->route('company.dashboard');
        }

        return $next($request);
    }
}
