<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMemberActive
{
    /**
     * Checks account status on every authenticated request.
     *
     * is_active = false            → permanently banned, immediate logout
     * suspended_until in the past  → suspension expired, auto-lifted
     * suspended_until in the future → temporarily suspended, limited access
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();

        // Permanently banned — log out immediately
        if ($user->isBanned()) {
            auth()->logout();
            return redirect()->route('login')
                ->with('error', 'Votre compte a été banni. Contactez un administrateur.');
        }

        // Suspension expired — lift it automatically and let the user through
        if ($user->suspended_until !== null && $user->suspended_until->isPast()) {
            $user->update(['suspended_until' => null]);
            return $next($request);
        }

        // Temporarily suspended — log out and show the suspension end date
        if ($user->isSuspended()) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $until = $user->suspended_until->format('d/m/Y à H:i');

            return redirect()->route('login')
                ->with('error', "Votre compte est suspendu jusqu'au {$until}. Contactez un administrateur.");
        }

        return $next($request);
    }
}
