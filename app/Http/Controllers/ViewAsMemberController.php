<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\UserRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Gère l'activation et la désactivation du mode "Naviguer en tant que Membre"
 * pour les administrateurs, super-administrateurs et modérateurs.
 */
class ViewAsMemberController extends Controller
{
    /**
     * Active le mode "Naviguer en tant que Membre" et redirige vers l'accueil.
     */
    public function enable(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_unless(
            $user !== null && $user->hasAnyRole([
                UserRole::SuperAdmin->value,
                UserRole::Admin->value,
                UserRole::Moderator->value,
            ]),
            403
        );

        session(['viewing_as_member' => true]);

        return redirect()->route('dashboard.member.overview')
            ->with('success', 'Vous naviguez désormais en tant que membre.');
    }

    /**
     * Désactive le mode "Naviguer en tant que Membre" et redirige vers le tableau de bord administrateur.
     */
    public function disable(Request $request): RedirectResponse
    {
        session()->forget('viewing_as_member');

        $user = $request->user();

        if (
            $user !== null && $user->hasAnyRole([
                UserRole::SuperAdmin->value,
                UserRole::Admin->value,
                UserRole::Moderator->value,
            ])
        ) {
            return redirect('/admin')
                ->with('success', 'Vous êtes de retour en mode administrateur.');
        }

        return redirect()->route('home')
            ->with('success', 'Vous êtes de retour en mode administrateur.');
    }
}
