<?php

namespace App\Http\Middleware;

use App\Models\Profile;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileComplete
{
    /**
     * Redirects to the profile page if the member has not yet created their profile.
     * Profile existence is cached for 10 minutes to avoid a SQL query on every request.
     * The cache is invalidated in EditProfile::save() after profile creation.
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var User|null $user */
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
            fn () => Profile::where('user_id', $userId)->exists()
        );
    }
}
