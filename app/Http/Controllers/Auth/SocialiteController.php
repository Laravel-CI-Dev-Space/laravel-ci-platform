<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\SocialiteService;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;
use App\Exceptions\AccountSuspendedException;


class SocialiteController extends Controller
{
    public function __construct(
        private readonly SocialiteService $socialiteService
    ) {}

    /**
     * Redirige l'utilisateur vers GitHub pour l'authentification.
     */
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('github')->redirect();
    }

    /**
     * Traite le callback GitHub après authentification.
     * Connecte le user et redirige vers son dashboard selon son rôle.
     */
    public function callback(): RedirectResponse
    {
        try {
            $githubUser = Socialite::driver('github')->user();
            $user = $this->socialiteService->findOrCreateUser($githubUser);

            auth()->login($user, remember: true);

            return redirect()->route('dashboard')
                ->with('success', "Bienvenue {$user->name} ! 👋");

        } catch (AccountSuspendedException $e) {
            // Compte suspendu — message explicite
            return redirect()->route('login')
                ->with('error', $e->getMessage());

        } catch (\Exception $e) {
            // Erreur GitHub OAuth — message générique
            return redirect()->route('login')
                ->with('error', 'Connexion GitHub échouée. Veuillez réessayer.');
        }
    }
}
