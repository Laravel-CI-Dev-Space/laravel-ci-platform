<?php

namespace App\Http\Middleware;

use App\Enums\UserPermission;
use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FilamentAdminAccess
{
    /**
     * Super-admin and admin roles always have panel access. Any other user
     * may also enter if a super-admin has granted them the "admin.access"
     * permission directly, regardless of their role.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user && ! $user->hasAnyRole([
            UserRole::SuperAdmin->value,
            UserRole::Admin->value,
        ]) && ! $user->can(UserPermission::AdminAccess->value)) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
