<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\CompanyAccount;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CompanyActive
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var CompanyAccount|null $account */
        $account = auth('company')->user();

        if ($account === null) {
            return redirect()->route('company.login');
        }

        if ($account->isPending()) {
            auth('company')->logout();

            return redirect()->route('company.login')
                ->with('warning', 'Votre compte est en cours de validation. Vous recevrez un email dès qu\'il sera activé.');
        }

        if ($account->isSuspended()) {
            auth('company')->logout();

            return redirect()->route('company.login')
                ->with('error', 'Votre compte a été suspendu. Veuillez contacter le support.');
        }

        if ($account->isRejected()) {
            auth('company')->logout();

            return redirect()->route('company.login')
                ->with('error', 'Votre demande d\'accès a été refusée. Veuillez contacter le support.');
        }

        return $next($request);
    }
}
