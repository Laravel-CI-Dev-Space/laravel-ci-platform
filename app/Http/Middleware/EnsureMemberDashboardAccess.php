<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Autorise l'accès au dashboard membre aux utilisateurs ayant le rôle
 * "member", ainsi qu'aux admin/super-admin/modérateur en mode
 * "Naviguer en tant que Membre" (voir ViewAsMemberController).
 */
class EnsureMemberDashboardAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user?->hasRole(UserRole::Member->value)) {
            return $next($request);
        }

        if (session('viewing_as_member') && $user?->hasAnyRole([
            UserRole::SuperAdmin->value,
            UserRole::Admin->value,
            UserRole::Moderator->value,
        ])) {
            return $next($request);
        }

        abort(403);
    }
}
