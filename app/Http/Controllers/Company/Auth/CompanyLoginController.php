<?php

declare(strict_types=1);

namespace App\Http\Controllers\Company\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CompanyLoginController extends Controller
{
    /**
     * Affiche le formulaire de connexion entreprise.
     */
    public function showLoginForm(): View
    {
        return view('company.auth.login');
    }

    /**
     * Authentifie le compte entreprise via le guard 'company'.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required'    => "L'adresse email est obligatoire.",
            'email.email'       => "L'adresse email est invalide.",
            'password.required' => 'Le mot de passe est obligatoire.',
        ]);

        if (Auth::guard('company')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Redirige vers le panel Filament entreprise (pas le dashboard membre).
            return redirect()->to('/company/portal');
        }

        return back()
            ->withInput($request->only('email'))
            ->with('error', 'Identifiants incorrects. Vérifiez votre email et mot de passe.');
    }

    /**
     * Déconnecte le compte entreprise.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('company')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('company.login')
            ->with('success', 'Vous avez été déconnecté avec succès.');
    }
}
