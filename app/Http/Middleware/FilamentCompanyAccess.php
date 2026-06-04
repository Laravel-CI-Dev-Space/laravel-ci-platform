<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware d'accès au panel Filament entreprise.
 * Remplace Filament\Http\Middleware\Authenticate car le guard est 'company'.
 */
class FilamentCompanyAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        // Non connecté → redirige vers le login entreprise
        if (! auth('company')->check()) {
            return redirect()->route('company.login')
                ->with('error', 'Veuillez vous connecter à votre espace entreprise.');
        }

        /** @var \App\Models\CompanyAccount $account */
        $account = auth('company')->user();

        // Compte inactif, suspendu ou rejeté → déconnexion
        if (! $account->isActive()) {
            auth('company')->logout();
            $request->session()->invalidate();

            return redirect()->route('company.login')
                ->with('error', 'Votre compte n\'est plus actif. Contactez le support.');
        }

        // Mot de passe temporaire non changé → page de changement obligatoire
        if ($account->mustChangePassword()) {
            return redirect()->route('company.password.change')
                ->with('warning', 'Vous devez définir un nouveau mot de passe avant de continuer.');
        }

        return $next($request);
    }
}
