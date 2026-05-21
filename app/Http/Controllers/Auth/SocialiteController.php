<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\AccountBannedException;
use App\Http\Controllers\Controller;
use App\Services\Auth\SocialiteService;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function __construct(
        private readonly SocialiteService $socialiteService
    ) {}

    public function redirect(): RedirectResponse
    {
        return Socialite::driver('github')->redirect();
    }

    public function callback(): RedirectResponse
    {
        try {
            $githubUser = Socialite::driver('github')->user();
            $user = $this->socialiteService->findOrCreateUser($githubUser);

            auth()->login($user, remember: true);

            return redirect()->route('dashboard')
                ->with('success', "Bienvenue {$user->name} !");

        } catch (AccountBannedException $e) {
            return redirect()->route('login')
                ->with('error', $e->getMessage());

        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Connexion GitHub échouée. Veuillez réessayer.');
        }
    }
}
