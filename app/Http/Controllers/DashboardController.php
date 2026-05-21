<?php

namespace App\Http\Controllers;

use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function redirect(): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        if ($user->hasRole('super-admin'))  return redirect()->route('dashboard.super-admin');
        if ($user->hasRole('admin'))        return redirect()->route('dashboard.admin');
        if ($user->hasRole('moderateur'))   return redirect()->route('dashboard.moderateur');
        if ($user->hasRole('membre-actif')) return redirect()->route('dashboard.membre');

        auth()->logout();

        return redirect()->route('login')->with('error', 'Accès non autorisé.');
    }

    public function adminPanel(): RedirectResponse
    {
        return redirect(Filament::getPanel('admin')->getUrl());
    }

    public function moderateur(): View
    {
        return view('dashboard.moderateur.index');
    }

    public function membre(): View
    {
        return view('dashboard.membre.index');
    }
}
