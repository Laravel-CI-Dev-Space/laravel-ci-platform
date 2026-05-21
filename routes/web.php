<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\CvController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DesignSystemController;
use Illuminate\Support\Facades\Route;

// ─── PAGE D'ACCUEIL ───────────────────────────────────────
Route::get('/', fn () => view('welcome'))->name('home');

// ─── AUTH GITHUB ─────────────────────────────────────────
Route::get('/login', fn () => view('auth.login'))
    ->name('login')
    ->middleware('guest');

Route::get('/auth/github/redirect', [SocialiteController::class, 'redirect'])
    ->name('auth.github.redirect')
    ->middleware('guest');

Route::get('/auth/github/callback', [SocialiteController::class, 'callback'])
    ->name('auth.github.callback')
    ->middleware('throttle:20,1');

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// ─── ROUTES AUTHENTIFIÉES ─────────────────────────────────
Route::middleware(['auth', 'active'])->group(function () {

    // Profil — accessible même sans profil complété
    Route::get('/profil/completer', \App\Livewire\EditProfile::class)
        ->name('profile.edit');

    // CV — sert le fichier depuis le disque privé avec vérification auth
    Route::get('/cv/{userId}', [CvController::class, 'download'])
        ->name('cv.download')
        ->whereNumber('userId');

    // ─── ROUTES NÉCESSITANT UN PROFIL COMPLET ─────────────
    Route::middleware('profile.complete')->group(function () {

        // Redirection vers le bon dashboard selon le rôle
        Route::get('/dashboard', [DashboardController::class, 'redirect'])
            ->name('dashboard');

        // Super admin
        Route::middleware('role:super-admin')
            ->prefix('dashboard/super-admin')
            ->group(function () {
                Route::get('/', [DashboardController::class, 'adminPanel'])
                    ->name('dashboard.super-admin');
            });

        // Admin
        Route::middleware('role:admin')
            ->prefix('dashboard/admin')
            ->group(function () {
                Route::get('/', [DashboardController::class, 'adminPanel'])
                    ->name('dashboard.admin');
            });

        // Modérateur
        Route::middleware('role:moderateur')
            ->prefix('dashboard/moderateur')
            ->group(function () {
                Route::get('/', [DashboardController::class, 'moderateur'])
                    ->name('dashboard.moderateur');
            });

        // Membre actif
        Route::middleware('role:membre-actif')
            ->prefix('dashboard/membre')
            ->group(function () {
                Route::get('/', [DashboardController::class, 'membre'])
                    ->name('dashboard.membre');
            });

    Route::get('design-system', [DesignSystemController::class, 'index']);
