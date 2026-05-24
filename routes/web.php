<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\CvController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DesignSystemController;
use App\Livewire\EditProfile;
use Illuminate\Support\Facades\Route;

// ─── HOME ──────────────────────────────────────────────────
Route::get('/', fn () => view('welcome'))->name('home');

// ─── AUTHENTICATION ────────────────────────────────────────
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

// ─── AUTHENTICATED ROUTES ──────────────────────────────────
Route::middleware(['auth', 'active'])->group(function () {

    // Profile — accessible even before completing the profile
    Route::get('/profil/completer', EditProfile::class)
        ->name('profile.edit');

    // CV download — served from private disk with authentication check
    Route::get('/cv/{userId}', [CvController::class, 'download'])
        ->name('cv.download')
        ->whereNumber('userId');

    // ─── ROUTES REQUIRING A COMPLETED PROFILE ──────────────
    Route::middleware('profile.complete')->group(function () {

        // Redirects to the role-specific dashboard
        Route::get('/dashboard', [DashboardController::class, 'redirect'])
            ->name('dashboard');

        Route::middleware('role:super-admin')->prefix('dashboard/super-admin')->group(function () {
            Route::get('/', [DashboardController::class, 'adminPanel'])->name('dashboard.super-admin');
        });

        Route::middleware('role:admin')->prefix('dashboard/admin')->group(function () {
            Route::get('/', [DashboardController::class, 'adminPanel'])->name('dashboard.admin');
        });

        Route::middleware('role:moderateur')->prefix('dashboard/moderateur')->group(function () {
            Route::get('/', [DashboardController::class, 'moderateur'])->name('dashboard.moderateur');
        });

        Route::middleware('role:membre-actif')->prefix('dashboard/membre')->group(function () {
            Route::get('/', [DashboardController::class, 'membre'])->name('dashboard.membre');
        });
    });
});

// ─── DESIGN SYSTEM (admin only) ────────────────────────────
Route::get('/design-system', [DesignSystemController::class, 'index'])
    ->middleware(['auth', 'role:super-admin|admin']);
