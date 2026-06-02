<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\Blog\ArticleController;
use App\Http\Controllers\Blog\ResourceController;
use App\Http\Controllers\CvController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DesignSystemController;
use App\Http\Controllers\Forum\AnswerController;
use App\Http\Controllers\Forum\QuestionController;
use App\Livewire\EditProfile;
use Illuminate\Support\Facades\Route;

// ─── PUBLIC WEB PAGES ──────────────────────────────────────
Route::get('/', fn () => view('web.home'))->name('home');
Route::get('/about', fn () => view('web.about'))->name('about');

// ─── FORUM — Routes publiques ──────────────────────────────
Route::prefix('forum')->name('forum.')->group(function () {
    Route::get('/', [QuestionController::class, 'index'])->name('index');

    // Protégé: créer une question
    Route::get('/ask', [QuestionController::class, 'create'])
        ->name('ask')
        ->middleware(['auth', 'active', 'profile.complete', 'role:member']);

    // Public: doit venir en dernier
    Route::get('/{slug}', [QuestionController::class, 'show'])->name('show');
});

// ─── FORUM — Routes authentifiées ─────────────────────────
Route::middleware(['auth', 'active', 'profile.complete', 'role:member'])->group(function () {
    Route::post('/forum/questions', [QuestionController::class, 'store'])
        ->name('forum.questions.store');
    Route::post('/forum/{question}/answers', [AnswerController::class, 'store'])
        ->name('forum.answers.store');

    // Édition et suppression d'une question par son auteur
    Route::get('/forum/{question}/edit', [QuestionController::class, 'edit'])
        ->name('forum.edit');
    Route::delete('/forum/{question}', [QuestionController::class, 'destroy'])
        ->name('forum.destroy');
});

// ─── BLOG — Routes publiques ───────────────────────────────
Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/', [ArticleController::class, 'index'])->name('index');

    // Protégé : soumettre un article
    Route::get('/submit', [ArticleController::class, 'create'])
        ->name('create')
        ->middleware(['auth', 'active', 'profile.complete', 'role:member']);

    // Public : doit venir en dernier
    Route::get('/{slug}', [ArticleController::class, 'show'])->name('show');
});

// ─── RESSOURCES — Routes publiques + auth ─────────────────
Route::prefix('resources')->name('resources.')->group(function () {
    Route::get('/', [ResourceController::class, 'index'])->name('index');

    Route::get('/{resource}/download', [ResourceController::class, 'download'])
        ->name('download')
        ->middleware(['auth', 'active']);
});

// ─── BLOG & RESSOURCES — Routes authentifiées ─────────────
Route::middleware(['auth', 'active', 'profile.complete', 'role:member'])->group(function () {
    Route::post('/blog/articles', [ArticleController::class, 'store'])
        ->name('blog.articles.store');
    Route::post('/blog/{article}/submit', [ArticleController::class, 'submit'])
        ->name('blog.articles.submit');
    Route::delete('/blog/{article}', [ArticleController::class, 'destroy'])
        ->name('blog.articles.destroy');
    Route::post('/resources', [ResourceController::class, 'store'])
        ->name('resources.store');
});

// ─── EVENTS ────────────────────────────────────────────────
Route::prefix('events')->name('events.')->group(function () {
    Route::get('/', fn () => view('web.events.index'))->name('index');
    Route::get('/{slug}', fn (string $slug) => view('web.events.show', compact('slug')))->name('show');
});

// ─── JOBS ──────────────────────────────────────────────────
Route::prefix('jobs')->name('jobs.')->group(function () {
    Route::get('/', fn () => view('web.jobs.index'))->name('index');

    // Protected
    Route::get('/post', fn () => view('web.jobs.index'))
        ->name('create')
        ->middleware(['auth', 'active', 'profile.complete']);
    Route::post('/{id}/apply', fn () => back())
        ->name('apply')
        ->middleware(['auth', 'active', 'profile.complete'])
        ->whereNumber('id');
    Route::delete('/{id}/unsave', fn () => back())
        ->name('unsave')
        ->middleware(['auth', 'active'])
        ->whereNumber('id');

    // Public
    Route::get('/{slug}', fn (string $slug) => view('web.jobs.show', compact('slug')))->name('show');
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
                Route::get('/', fn () => view('dashboard.member.overview'))->name('overview');
                Route::get('/questions', function () {
                    $questions = auth()->user()
                        ->questions()
                        ->with('tags')
                        ->latest()
                        ->paginate(15);

                    return view('dashboard.member.questions', compact('questions'));
                })->name('questions');
                Route::get('/articles', function () {
                    $articles = auth()->user()
                        ->articles()
                        ->with('tags')
                        ->latest()
                        ->paginate(15);

                    return view('dashboard.member.articles', compact('articles'));
                })->name('articles');
                Route::get('/events', fn () => view('dashboard.member.events'))->name('events');
                Route::get('/applications', fn () => view('dashboard.member.applications'))->name('applications');
                Route::get('/favorites', fn () => view('dashboard.member.favorites'))->name('favorites');
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
