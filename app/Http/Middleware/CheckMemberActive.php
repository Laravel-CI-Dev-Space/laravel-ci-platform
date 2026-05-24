<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMemberActive
{
    /**
     * Vérifie l'état du compte à chaque requête authentifiée.
     *
     * is_active = false      → banni définitivement, logout immédiat
     * suspended_until futur  → suspendu temporairement, accès dashboard limité
     * suspended_until passé  → lève la suspension automatiquement
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();

        // Banni définitivement — logout immédiat
        if ($user->isBanned()) {
            auth()->logout();

            return redirect()->route('login')
                ->with('error', 'Votre compte a été banni. Contactez un administrateur.');
        }

        // Suspension expirée — on la lève automatiquement
        if ($user->suspended_until !== null && $user->suspended_until->isPast()) {
            $user->update(['suspended_until' => null]);
        }

        // Suspendu temporairement — accès dashboard mais flag en session
        if ($user->isSuspended()) {
            session(['suspended' => true, 'suspended_until' => $user->suspended_until]);
        } else {
            session()->forget(['suspended', 'suspended_until']);
        }

        return $next($request);
    }
}
