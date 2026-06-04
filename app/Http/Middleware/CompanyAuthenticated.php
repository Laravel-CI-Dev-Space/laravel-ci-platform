<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CompanyAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth('company')->check()) {
            return redirect()->route('company.login')
                ->with('error', 'Veuillez vous connecter pour accéder à cet espace.');
        }

        return $next($request);
    }
}
