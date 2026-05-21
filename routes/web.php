<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DesignSystemController;

// ─── PAGE D'ACCUEIL ───────────────────────────────────────
Route::get('/', fn () => view('welcome'))->name('home');

// ─── AUTH GITHUB ─────────────────────────────────────────
Route::get('/login', fn () => view('auth.login'))
    ->name('login')
    ->middleware('guest');

Route::get('/auth/github/redirect', [SocialiteController::class, 'redirect'])
    ->name('auth.github.redirect');

Route::get('/auth/github/callback', [SocialiteController::class, 'callback'])
    ->name('auth.github.callback');

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// ─── PROFIL ───────────────────────────────────────────────
Route::middleware(['auth', 'active'])
    ->group(function () {
        Route::get('/profil/completer', \App\Livewire\EditProfile::class)
            ->name('profile.edit');
    });

// ─── REDIRECT DASHBOARD SELON RÔLE ───────────────────────
Route::get('/dashboard', [DashboardController::class, 'redirect'])
    ->name('dashboard')
    ->middleware(['auth', 'active', 'profile.complete']);

// ─── SUPER ADMIN ──────────────────────────────────────────
Route::middleware(['auth', 'active', 'profile.complete', 'role:super-admin'])
    ->prefix('dashboard/super-admin')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'adminPanel'])->name('dashboard.super-admin');
    });

// ─── ADMIN ────────────────────────────────────────────────
Route::middleware(['auth', 'active', 'profile.complete', 'role:admin'])
    ->prefix('dashboard/admin')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'adminPanel'])->name('dashboard.admin');
    });

// ─── MODÉRATEUR ───────────────────────────────────────────
Route::middleware(['auth', 'active', 'profile.complete', 'role:moderateur'])
    ->prefix('dashboard/moderateur')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'moderateur'])->name('dashboard.moderateur');
    });

// ─── MEMBRE ACTIF ─────────────────────────────────────────
Route::middleware(['auth', 'active', 'profile.complete', 'role:membre-actif'])
    ->prefix('dashboard/membre')
    ->group(function () {
        Route::get('/', fn () => view('dashboard.membre.index'))->name('dashboard.membre');
    });

Route::get('design-system', [DesignSystemController::class, 'index']);
