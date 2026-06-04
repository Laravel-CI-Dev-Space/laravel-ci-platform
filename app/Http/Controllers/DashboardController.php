<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /** Redirects the authenticated user to their role-specific dashboard. */
    public function redirect(): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->hasRole(UserRole::SuperAdmin->value)) {
            return redirect()->route('dashboard.super-admin');
        }
        if ($user->hasRole(UserRole::Admin->value)) {
            return redirect()->route('dashboard.admin');
        }
        if ($user->hasRole(UserRole::Moderator->value)) {
            return redirect()->route('dashboard.moderator.overview');
        }
        if ($user->hasRole(UserRole::Member->value)) {
            return redirect()->route('dashboard.member.overview');
        }

        Auth::logout();

        return redirect()->route('login')->with('error', 'Accès non autorisé.');
    }

    /** Redirects admins and super-admins to the Filament admin panel. */
    public function adminPanel(): RedirectResponse
    {
        return redirect(Filament::getPanel('admin')->getUrl());
    }
}
