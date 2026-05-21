<?php

namespace App\Services\Auth;

use App\Exceptions\AccountSuspendedException;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Contracts\User as GithubUser;

class SocialiteService
{
    public function __construct(
        private readonly NotificationService $notificationService
    ) {}

    /**
     * Trouve un user existant ou en crée un nouveau depuis GitHub.
     */
    public function findOrCreateUser(GithubUser $githubUser): User
    {
        // Chercher par github_id d'abord (le plus fiable)
        $user = User::where('github_id', $githubUser->getId())->first();

        if ($user) {
            return $this->updateUser($user, $githubUser);
        }

        // Chercher par email si github_id pas trouvé
        $user = User::where('email', $githubUser->getEmail())->first();

        if ($user) {
            return $this->updateUser($user, $githubUser);
        }

        // Créer un nouveau user
        return $this->createUser($githubUser);
    }

    /**
     * Crée un nouveau membre depuis les données GitHub.
     */
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

        $user->assignRole('membre-actif');

        // Envoyer l'email de bienvenue
        $this->notificationService->sendWelcome($user);

        Log::info("Nouveau membre créé : {$user->github_username}");

        return $user;
    }

    /**
     * Met à jour les infos GitHub d'un user existant.
     *
     * Seul le ban définitif (is_active = false) bloque la connexion.
     * La suspension temporaire (suspended_until) laisse passer — accès dashboard limité.
     */
    private function updateUser(User $user, GithubUser $githubUser): User
    {
        // Ban définitif — bloque la connexion
        if ($user->isBanned()) {
            throw new AccountSuspendedException();
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
