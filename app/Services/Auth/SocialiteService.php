<?php

namespace App\Services\Auth;

use App\Exceptions\AccountBannedException;
use App\Models\User;
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
     * Trouve un user existant ou en crée un nouveau depuis GitHub.
     * Enveloppé dans une transaction pour garantir que User + rôle sont atomiques.
     */
    public function findOrCreateUser(GithubUser $githubUser): User
    {
        $created = false;

        $user = DB::transaction(function () use ($githubUser, &$created) {
            $user = User::where('github_id', $githubUser->getId())->first()
                 ?? User::where('email', $githubUser->getEmail())->first();

            if ($user) {
                return $this->updateUser($user, $githubUser);
            }

            $created = true;

            return $this->createUser($githubUser);
        });

        // Envoi du mail APRÈS la transaction — évite le dispatch sur rollback
        if ($created) {
            $this->notificationService->sendWelcome($user);
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

        $user->assignRole('membre-actif');

        Log::info("Nouveau membre créé : {$user->github_username}");

        return $user;
    }

    /**
     * Met à jour les infos GitHub d'un user existant.
     * Le ban définitif (is_active = false) bloque la connexion.
     * La suspension temporaire laisse passer — accès dashboard limité.
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
