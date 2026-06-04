<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\CompanyAccount;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CompanyMustChangePassword
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var CompanyAccount|null $account */
        $account = auth('company')->user();

        if ($account !== null && $account->mustChangePassword()) {
            // Évite la boucle infinie sur la page de changement
            if (! $request->routeIs('company.password.change', 'company.password.update', 'company.logout')) {
                return redirect()->route('company.password.change')
                    ->with('warning', 'Vous devez définir un nouveau mot de passe avant de continuer.');
            }
        }

        return $next($request);
    }
}
