<?php

declare(strict_types=1);

namespace App\Http\Controllers\Company\Auth;

use App\Http\Controllers\Controller;
use App\Models\CompanyAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CompanyPasswordController extends Controller
{
    /**
     * Affiche le formulaire de définition du nouveau mot de passe.
     */
    public function showChangeForm(): View
    {
        return view('company.auth.change-password');
    }

    /**
     * Valide et enregistre le nouveau mot de passe.
     * Définit password_changed_at pour lever l'obligation de changement.
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string', 'min:10', 'confirmed'],
        ], [
            'password.required'  => 'Le nouveau mot de passe est obligatoire.',
            'password.min'       => 'Le mot de passe doit contenir au moins :min caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ]);

        /** @var CompanyAccount $account */
        $account = Auth::guard('company')->user();

        $account->update([
            'password'            => $request->input('password'),
            'password_changed_at' => now(),
        ]);

        return redirect()->to('/company/portal')
            ->with('success', 'Mot de passe mis à jour avec succès. Bienvenue sur votre espace entreprise !');
    }
}
