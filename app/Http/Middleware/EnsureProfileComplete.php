<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileComplete
{
    /**
     * Redirige vers la page profil si le membre n'a pas encore créé son profil.
     * Cache 10 min l'existence du profil pour éviter une requête SQL sur chaque requête.
     * Le cache est invalidé dans EditProfile::save() après création.
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();

        if (
            $user
            && ! $this->hasProfile($user->id)
            && ! $request->routeIs('profile.edit')
            && ! $request->routeIs('logout')
            && ! $request->is('admin*')
        ) {
            return redirect()->route('profile.edit');
        }

        return $next($request);
    }

    private function hasProfile(int $userId): bool
    {
        return Cache::remember(
            "user_has_profile_{$userId}",
            600,
            fn () => \App\Models\Profile::where('user_id', $userId)->exists()
        );
    }
}
