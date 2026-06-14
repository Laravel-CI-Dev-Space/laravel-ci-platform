<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\CvController;
use App\Http\Controllers\Dashboard\MemberJobApplicationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DesignSystemController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\JobOfferController;
use App\Http\Controllers\VitrineController;
use App\Livewire\EditProfile;
use Illuminate\Support\Facades\Route;

// ─── PUBLIC WEB PAGES ──────────────────────────────────────
Route::get('/', [VitrineController::class, 'home'])->name('home');
Route::get('/about', [VitrineController::class, 'about'])->name('about');
Route::get('/join', fn () => view('web.join'))->name('join');

Route::get('/robots.txt', function () {
    $lines = [
        'User-agent: *',
        'Disallow: /dashboard',
        'Disallow: /admin',
        'Disallow: /profil',
        '',
        'Sitemap: ' . url('/sitemap.xml'),
    ];

    return response(implode("\n", $lines), 200, ['Content-Type' => 'text/plain']);
});

// ─── FORUM ─────────────────────────────────────────────────
Route::prefix('forum')->name('forum.')->group(function () {
    Route::get('/', fn () => view('web.forum.index'))->name('index');

    // Protected: create a question
    Route::get('/ask', fn () => view('web.forum.index'))
        ->name('create')
        ->middleware(['auth', 'active', 'profile.complete']);

    // Protected: edit / delete own question
    Route::patch('/{slug}', fn () => back())
        ->name('edit')
        ->middleware(['auth', 'active', 'profile.complete']);
    Route::delete('/{slug}', fn () => redirect()->route('forum.index'))
        ->name('destroy')
        ->middleware(['auth', 'active', 'profile.complete']);

    // Public: must come last to avoid catching /ask /edit etc.
    Route::get('/{slug}', fn (string $slug) => view('web.forum.show', compact('slug')))->name('show');
});

// ─── BLOG ──────────────────────────────────────────────────
Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/', fn () => view('web.blog.index'))->name('index');

    // Protected
    Route::get('/write', fn () => view('web.blog.index'))
        ->name('create')
        ->middleware(['auth', 'active', 'profile.complete']);
    Route::get('/{slug}/edit', fn (string $slug) => view('web.blog.show', compact('slug')))
        ->name('edit')
        ->middleware(['auth', 'active', 'profile.complete']);
    Route::delete('/{slug}', fn () => redirect()->route('blog.index'))
        ->name('destroy')
        ->middleware(['auth', 'active', 'profile.complete']);

    // Public
    Route::get('/{slug}', fn (string $slug) => view('web.blog.show', compact('slug')))->name('show');
});

// ─── EVENTS (Sprint Roger — M4) ───────────────────────────
Route::controller(EventController::class)
    ->prefix('events')
    ->name('events.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{event:slug}', 'show')->name('show');
        Route::post('/{event:slug}/register', 'register')
            ->name('register')
            ->middleware(['auth', 'active', 'role:member']);
        Route::post('/{event:slug}/cancel', 'cancel')
            ->name('cancel')
            ->middleware(['auth', 'active', 'role:member']);
        Route::get('/{event:slug}/calendar.ics', 'calendar')
            ->name('calendar')
            ->middleware(['auth', 'active', 'role:member']);
    });

// ─── JOB BOARD (Sprint Roger — M5) ─────────────────────────
Route::controller(JobOfferController::class)
    ->prefix('jobs')
    ->name('jobs.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')
            ->name('create')
            ->middleware(['auth', 'active', 'role:member']);
        Route::post('/', 'store')
            ->name('store')
            ->middleware(['auth', 'active', 'role:member']);
        Route::get('/{jobOffer:slug}', 'show')->name('show');
        Route::post('/{jobOffer:slug}/apply', 'apply')
            ->name('apply')
            ->middleware(['auth', 'active', 'role:member']);
    });

// ─── MEMBER PUBLIC PROFILE ─────────────────────────────────
Route::get('/members/{username}', fn (string $username) => view('web.members.show', compact('username')))
    ->name('members.show');

// ─── AUTHENTICATION ────────────────────────────────────────
Route::get('/login', fn () => view('web.login'))
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

    // Profile completion — accessible before completing profile
    Route::get('/profil/completer', EditProfile::class)
        ->name('profile.edit');

    // CV download — served from private disk
    Route::get('/cv/{userId}', [CvController::class, 'download'])
        ->name('cv.download')
        ->whereNumber('userId');

    // ─── ROUTES REQUIRING A COMPLETED PROFILE ──────────────
    Route::middleware('profile.complete')->group(function () {

        // Role dispatcher — redirects to the right dashboard
        Route::get('/dashboard', [DashboardController::class, 'redirect'])
            ->name('dashboard');

        // ─── MEMBER DASHBOARD ──────────────────────────────
        Route::middleware('role:member')
            ->prefix('dashboard/member')
            ->name('dashboard.member.')
            ->group(function () {
                Route::get('/', [DashboardController::class, 'memberOverview'])->name('overview');
                Route::get('/questions', fn () => view('dashboard.member.questions'))->name('questions');
                Route::get('/articles', fn () => view('dashboard.member.articles'))->name('articles');
                Route::get('/events', [DashboardController::class, 'memberEvents'])->name('events');
                Route::get('/applications', [DashboardController::class, 'memberApplications'])->name('applications');
                Route::get('/applications/{jobApplication}', [MemberJobApplicationController::class, 'show'])->name('applications.show');
                Route::get('/favorites', [DashboardController::class, 'memberFavorites'])->name('favorites');
                Route::get('/alerts', [DashboardController::class, 'memberAlerts'])->name('alerts');
                Route::get('/profile', fn () => view('dashboard.member.profile'))->name('profile');
                Route::post('/profile', fn () => back())->name('profile.update');
            });

        // ─── MODERATOR DASHBOARD ───────────────────────────
        Route::middleware('role:moderator')
            ->prefix('dashboard/moderator')
            ->name('dashboard.moderator.')
            ->group(function () {
                Route::get('/', fn () => view('dashboard.moderator.overview'))->name('overview');
                Route::get('/reports', fn () => view('dashboard.moderator.reports'))->name('reports');
                Route::patch('/reports/{id}/resolve', fn () => back())->name('reports.resolve')->whereNumber('id');
                Route::patch('/reports/{id}/dismiss', fn () => back())->name('reports.dismiss')->whereNumber('id');
                Route::get('/questions', fn () => view('dashboard.moderator.questions'))->name('questions');
                Route::patch('/questions/{id}/pin', fn () => back())->name('questions.pin')->whereNumber('id');
                Route::get('/articles', fn () => view('dashboard.moderator.articles'))->name('articles');
            });

        // ─── SUPER-ADMIN / ADMIN → Filament panel ──────────
        Route::middleware('role:super-admin')->prefix('dashboard/super-admin')->group(function () {
            Route::get('/', [DashboardController::class, 'adminPanel'])->name('dashboard.super-admin');
        });

        Route::middleware('role:admin')->prefix('dashboard/admin')->group(function () {
            Route::get('/', [DashboardController::class, 'adminPanel'])->name('dashboard.admin');
        });
    });
});

// ─── DESIGN SYSTEM (admin only) ────────────────────────────
Route::get('/design-system', [DesignSystemController::class, 'index'])
    ->middleware(['auth', 'role:super-admin|admin']);
