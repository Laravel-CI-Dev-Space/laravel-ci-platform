<?php

namespace App\Services\Auth;

use App\Enums\UserRole;
use App\Exceptions\AccountBannedException;
use App\Exceptions\AccountEmailConflictException;
use App\Models\User;
use App\Services\Analytics\AnalyticsService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Contracts\User as GithubUser;

class SocialiteService
{
    public function __construct(
        private readonly NotificationService $notificationService
    ) {}

    /**
     * Finds an existing user or creates a new one from GitHub OAuth data.
     * Wrapped in a DB transaction so User + role assignment are atomic.
     * Welcome email is dispatched after the transaction to avoid queue dispatch on rollback.
     */
    public function findOrCreateUser(GithubUser $githubUser): User
    {
        $created = false;

        $user = DB::transaction(function () use ($githubUser, &$created) {
            // Le seul identifiant fiable et stable est le github_id : un email
            // ne doit jamais permettre de rattacher la connexion à un compte
            // existant (un compte GitHub différent pourrait revendiquer le
            // même email public et ainsi prendre le contrôle d'un compte).
            $user = User::where('github_id', $githubUser->getId())->first();

            if ($user) {
                return $this->updateUser($user, $githubUser);
            }

            // Empêche la création d'un doublon si l'email est déjà utilisé
            // par un autre compte (lié à un autre github_id).
            if (User::where('email', $githubUser->getEmail())->exists()) {
                throw new AccountEmailConflictException;
            }

            $created = true;

            return $this->createUser($githubUser);
        });

        if ($created) {
            $this->notificationService->sendWelcome($user);
            app(AnalyticsService::class)->trackEvent(
                type: 'auth_register',
                userId: $user->id,
                metadata: ['name' => $user->name],
            );
        } else {
            app(AnalyticsService::class)->trackEvent(
                type: 'auth_login',
                userId: $user->id,
            );
        }

        return $user;
    }

    private function createUser(GithubUser $githubUser): User
    {
        $user = User::create([
            'name'              => $githubUser->getName() ?? $githubUser->getNickname(),
            'email'             => $githubUser->getEmail(),
            'avatar'            => $githubUser->getAvatar(),
            'github_id'         => $githubUser->getId(),
            'github_username'   => $githubUser->getNickname(),
            'is_active'         => true,
            'email_verified_at' => now(),
            'last_login_at'     => now(),
        ]);

        $user->assignRole(UserRole::Member->value);

        Log::info("New member registered: {$user->github_username}");

        return $user;
    }

    /**
     * Updates GitHub data for a returning user.
     * Only a permanent ban (is_active = false) blocks login.
     * Temporary suspension passes through - dashboard access is limited instead.
     */
    private function updateUser(User $user, GithubUser $githubUser): User
    {
        if ($user->isBanned()) {
            throw new AccountBannedException;
        }

        $user->update([
            'name'            => $githubUser->getName() ?? $githubUser->getNickname(),
            'avatar'          => $githubUser->getAvatar(),
            'github_id'       => $githubUser->getId(),
            'github_username' => $githubUser->getNickname(),
            'last_login_at'   => now(),
        ]);

        return $user;
    }
}
