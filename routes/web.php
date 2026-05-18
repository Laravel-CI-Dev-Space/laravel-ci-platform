<?php

use App\Http\Controllers\Auth\SocialiteController;
use Illuminate\Support\Facades\Route;

// ─── PAGE D'ACCUEIL ───────────────────────────────────────
Route::get('/', function () {
    return view('welcome');
})->name('home');

// ─── AUTH GITHUB ─────────────────────────────────────────
Route::get('/login', function () {
    return view('auth.login');
})->name('login')->middleware('guest');

Route::get('/auth/github/redirect', [SocialiteController::class, 'redirect'])
    ->name('auth.github.redirect');

Route::get('/auth/github/callback', [SocialiteController::class, 'callback'])
    ->name('auth.github.callback');

Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('home');
})->name('logout')->middleware('auth');

// ─── REDIRECT DASHBOARD SELON RÔLE ───────────────────────
Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->hasRole('super-admin')) {
        return redirect()->route('dashboard.super-admin');
    }

    if ($user->hasRole('admin')) {
        return redirect()->route('dashboard.admin');
    }

    if ($user->hasRole('moderateur')) {
        return redirect()->route('dashboard.moderateur');
    }

    if ($user->hasRole('membre-actif')) {
        return redirect()->route('dashboard.membre');
    }

    // Rôle inconnu
    auth()->logout();
    return redirect()->route('login')
        ->with('error', 'Accès non autorisé.');

})->name('dashboard')->middleware(['auth', 'active']);

// ─── ZONE SUPER ADMIN ────────────────────────────────────
Route::middleware(['auth', 'active', 'role:super-admin'])
    ->prefix('dashboard/super-admin')
    ->group(function () {
        Route::get('/', function () {
            return redirect('/admin');
        })->name('dashboard.super-admin');
    });

// ─── ZONE ADMIN ──────────────────────────────────────────
Route::middleware(['auth', 'active', 'role:admin'])
    ->prefix('dashboard/admin')
    ->group(function () {
        Route::get('/', function () {
            return redirect('/admin');
        })->name('dashboard.admin');
    });

// ─── ZONE MODÉRATEUR ─────────────────────────────────────
Route::middleware(['auth', 'active', 'role:moderateur'])
    ->prefix('dashboard/moderateur')
    ->group(function () {
        Route::get('/', function () {
            return view('dashboard.moderateur.index');
        })->name('dashboard.moderateur');
    });

// ─── ZONE MEMBRE ACTIF ───────────────────────────────────
// Suspendu temporairement = accès autorisé mais bannière d'avertissement
Route::middleware(['auth', 'active', 'role:membre-actif'])
    ->prefix('dashboard/membre')
    ->group(function () {
        Route::get('/', function () {
            return view('dashboard.membre.index');
        })->name('dashboard.membre');
    });
