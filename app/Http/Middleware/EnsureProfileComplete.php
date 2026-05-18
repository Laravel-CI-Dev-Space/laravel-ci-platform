<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileComplete
{
    /**
     * Redirige vers la page profil si le membre n'a pas encore
     * créé son profil (première connexion).
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (
            auth()->check()
            && ! auth()->user()->hasCompletedProfile()
            && ! $request->routeIs('profile.edit')
            && ! $request->routeIs('logout')
        ) {
            return redirect()->route('profile.edit');
        }

        return $next($request);
    }
}
