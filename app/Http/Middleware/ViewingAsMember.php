<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Permet à un admin/super-admin/modérateur de naviguer le site
 * en simulant l'expérience d'un membre, pour tester les fonctionnalités.
 * Le flag est stocké en session et n'altère pas les permissions réelles —
 * il sert uniquement à l'affichage du bandeau et à la redirection du dashboard.
 */
class ViewingAsMember
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Si l'user n'a plus les droits admin (ex: rétrogradé), désactiver le mode
        if (
            session('viewing_as_member') && $user && ! $user->hasAnyRole([
                UserRole::SuperAdmin->value,
                UserRole::Admin->value,
                UserRole::Moderator->value,
            ])
        ) {
            session()->forget('viewing_as_member');
        }

        return $next($request);
    }
}
