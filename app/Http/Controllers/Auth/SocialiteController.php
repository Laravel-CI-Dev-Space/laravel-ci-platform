<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\AccountBannedException;
use App\Exceptions\AccountEmailConflictException;
use App\Http\Controllers\Controller;
use App\Services\Auth\SocialiteService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

class SocialiteController extends Controller
{
    public function __construct(
        private readonly SocialiteService $socialiteService
    ) {}

    /** Redirects the user to GitHub for authentication. */
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('github')->redirect();
    }

    /** Handles the GitHub OAuth callback, logs the user in and redirects to their dashboard. */
    public function callback(): RedirectResponse
    {
        try {
            $githubUser = Socialite::driver('github')->user();
            $user       = $this->socialiteService->findOrCreateUser($githubUser);

            auth()->login($user, remember: true);

            return redirect()->route('dashboard')
                ->with('success', "Bienvenue {$user->name} !");

        } catch (AccountBannedException|AccountEmailConflictException $e) {
            return redirect()->route('login')
                ->with('error', $e->getMessage());

        } catch (InvalidStateException $e) {
            Log::warning('GitHub OAuth: invalid state (possible session mismatch or double-click)', [
                'url'        => request()->fullUrl(),
                'session_id' => session()->getId(),
            ]);

            return redirect()->route('login')
                ->with('error', 'Session OAuth expirée. Veuillez réessayer.');

        } catch (\Exception $e) {
            Log::error('GitHub OAuth callback failed', [
                'exception' => get_class($e),
                'message'   => $e->getMessage(),
                'trace'     => $e->getTraceAsString(),
            ]);

            return redirect()->route('login')
                ->with('error', 'Connexion GitHub échouée. Veuillez réessayer.');
        }
    }
}
