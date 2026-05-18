<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\SocialiteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DesignSystemController;

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

// ─── PROFIL ───────────────────────────────────────────────
Route::middleware(['auth', 'active'])
    ->group(function () {
        Route::get('/profil/completer', \App\Livewire\EditProfile::class)
            ->name('profile.edit');
    });

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

    auth()->logout();
    return redirect()->route('login')
        ->with('error', 'Accès non autorisé.');

})->name('dashboard')->middleware(['auth', 'active', 'profile.complete']);

// ─── SUPER ADMIN ──────────────────────────────────────────
Route::middleware(['auth', 'active', 'profile.complete', 'role:super-admin'])
    ->prefix('dashboard/super-admin')
    ->group(function () {
        Route::get('/', fn () => redirect('/admin'))->name('dashboard.super-admin');
    });

// ─── ADMIN ────────────────────────────────────────────────
Route::middleware(['auth', 'active', 'profile.complete', 'role:admin'])
    ->prefix('dashboard/admin')
    ->group(function () {
        Route::get('/', fn () => redirect('/admin'))->name('dashboard.admin');
    });

// ─── MODÉRATEUR ───────────────────────────────────────────
Route::middleware(['auth', 'active', 'profile.complete', 'role:moderateur'])
    ->prefix('dashboard/moderateur')
    ->group(function () {
        Route::get('/', fn () => view('dashboard.moderateur.index'))->name('dashboard.moderateur');
    });

// ─── MEMBRE ACTIF ─────────────────────────────────────────
Route::middleware(['auth', 'active', 'profile.complete', 'role:membre-actif'])
    ->prefix('dashboard/membre')
    ->group(function () {
        Route::get('/', fn () => view('dashboard.membre.index'))->name('dashboard.membre');
    });

    Route::get('design-system', [DesignSystemController::class, 'index']);