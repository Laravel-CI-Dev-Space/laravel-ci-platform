<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\MemberExportController;
use App\Http\Controllers\Admin\RegistrationExportController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\Blog\ArticleController;
use App\Http\Controllers\Blog\ResourceController;
use App\Http\Controllers\Company\ApplicationController as CompanyApplicationController;
use App\Http\Controllers\Company\Auth\CompanyLoginController;
use App\Http\Controllers\Company\Auth\CompanyPasswordController;
use App\Http\Controllers\Company\Auth\CompanyRegisterController;
use App\Http\Controllers\Company\JobOfferController as CompanyJobOfferController;
use App\Http\Controllers\CvController;
use App\Http\Controllers\Dashboard\MemberDashboardController;
use App\Http\Controllers\Dashboard\ModeratorDashboardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DesignSystemController;
use App\Http\Controllers\Events\EventController;
use App\Http\Controllers\Events\EventRegistrationController;
use App\Http\Controllers\Events\GuestRegistrationController;
use App\Http\Controllers\Forum\AnswerController;
use App\Http\Controllers\Forum\QuestionController;
use App\Http\Controllers\Jobs\JobApplicationController;
use App\Http\Controllers\Jobs\JobOfferController;
use App\Http\Controllers\Search\SearchController;
use App\Http\Controllers\ViewAsMemberController;
use App\Http\Controllers\Web\AboutController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\MemberProfileController;
use App\Livewire\EditProfile;
use Illuminate\Support\Facades\Route;

// ─── PUBLIC WEB PAGES ──────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/join', fn () => view('web.join'))->name('join');

Route::get('/newsletter/unsubscribe/{token}', function (string $token) {
    $subscriber = \App\Models\NewsletterSubscriber::where('token', $token)->firstOrFail();
    $subscriber->unsubscribe();

    return view('web.newsletter-unsubscribed');
})->name('newsletter.unsubscribe');

Route::get('/robots.txt', function () {
    return response(implode("\n", [
        'User-agent: *',
        'Disallow: /dashboard',
        'Disallow: /admin',
        'Disallow: /profil',
        '',
        'Sitemap: ' . url('/sitemap.xml'),
    ]), 200, ['Content-Type' => 'text/plain']);
});

// ─── SEARCH ────────────────────────────────────────────────
Route::get('/search', [SearchController::class, 'index'])->name('search.index');

// ─── FORUM — Routes publiques ──────────────────────────────
Route::prefix('forum')->name('forum.')->group(function () {
    Route::get('/', [QuestionController::class, 'index'])->name('index');
    Route::get('/ask', [QuestionController::class, 'create'])
        ->name('ask')
        ->middleware(['auth', 'active', 'profile.complete', 'permission:forum.question.create']);
    Route::get('/{slug}', [QuestionController::class, 'show'])->name('show');
});

// ─── FORUM — Routes authentifiées ─────────────────────────
Route::middleware(['auth', 'active', 'profile.complete'])->group(function () {
    Route::post('/forum/questions', [QuestionController::class, 'store'])
        ->middleware(['throttle:10,1', 'permission:forum.question.create'])
        ->name('forum.questions.store');
    Route::post('/forum/{question}/answers', [AnswerController::class, 'store'])
        ->middleware(['throttle:20,1', 'permission:forum.answer.create'])
        ->name('forum.answers.store');
    Route::get('/forum/{question}/edit', [QuestionController::class, 'edit'])
        ->middleware('permission:forum.question.edit')
        ->name('forum.edit');
    Route::delete('/forum/{question}', [QuestionController::class, 'destroy'])
        ->middleware('permission:forum.question.delete')
        ->name('forum.destroy');
});

// ─── BLOG — Routes publiques ───────────────────────────────
Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/', [ArticleController::class, 'index'])->name('index');
    Route::get('/submit', [ArticleController::class, 'create'])
        ->name('create')
        ->middleware(['auth', 'active', 'profile.complete', 'permission:blog.article.create']);
    Route::get('/{slug}', [ArticleController::class, 'show'])->name('show');
});

// ─── RESSOURCES ────────────────────────────────────────────
Route::prefix('resources')->name('resources.')->group(function () {
    Route::get('/', [ResourceController::class, 'index'])->name('index');
    Route::get('/{resource}/download', [ResourceController::class, 'download'])
        ->name('download')
        ->middleware(['auth', 'active']);
});

// ─── BLOG & RESSOURCES — Routes authentifiées ─────────────
Route::middleware(['auth', 'active', 'profile.complete'])->group(function () {
    Route::post('/blog/articles', [ArticleController::class, 'store'])
        ->middleware(['throttle:10,1', 'permission:blog.article.create'])
        ->name('blog.articles.store');
    Route::get('/blog/{article}/edit', [ArticleController::class, 'edit'])
        ->middleware('permission:blog.article.edit')
        ->name('blog.articles.edit');
    Route::post('/blog/{article}/submit', [ArticleController::class, 'submit'])
        ->middleware('permission:blog.article.submit')
        ->name('blog.articles.submit');
    Route::delete('/blog/{article}', [ArticleController::class, 'destroy'])
        ->middleware('permission:blog.article.delete')
        ->name('blog.articles.destroy');
    Route::post('/resources', [ResourceController::class, 'store'])
        ->middleware('permission:blog.resource.upload')
        ->name('resources.store');
});

// ─── EVENTS — Routes publiques ────────────────────────────
Route::prefix('events')->name('events.')->group(function () {
    Route::get('/', [EventController::class, 'index'])->name('index');
    Route::get('/{slug}', [EventController::class, 'show'])->name('show');
    Route::post('/{event}/guest-register', [GuestRegistrationController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('guest.register');
    Route::get('/ticket/{token}', fn (string $token) => view('web.events.ticket-verify', compact('token')))
        ->name('ticket.verify');
});

// ─── EVENTS — Routes authentifiées ────────────────────────
Route::middleware(['auth', 'active', 'profile.complete'])->group(function () {
    Route::post('/events/{event}/register', [EventRegistrationController::class, 'store'])->name('events.register');
    Route::delete('/events/registrations/{registration}', [EventRegistrationController::class, 'destroy'])->name('events.cancel');
    Route::get('/events/registrations/{registration}/ical', [EventRegistrationController::class, 'downloadIcal'])->name('events.ical');
});

// ─── JOB BOARD — Routes publiques ────────────────────────
Route::prefix('jobs')->name('jobs.')->group(function () {
    Route::get('/', [JobOfferController::class, 'index'])->name('index');
    Route::get('/{slug}', [JobOfferController::class, 'show'])->name('show');
});

// ─── JOB BOARD — Routes authentifiées ────────────────────
Route::middleware(['auth', 'active', 'profile.complete'])->group(function () {
    Route::post('/jobs/{offer}/apply', [JobApplicationController::class, 'store'])
        ->middleware(['throttle:10,1', 'permission:job.apply'])
        ->name('jobs.applications.store');
    Route::post('/jobs/{offer}/favorite', [JobOfferController::class, 'toggleFavorite'])
        ->middleware('permission:job.favorite')
        ->name('jobs.favorite');
});

// ─── MEMBER PUBLIC PROFILE ─────────────────────────────────
Route::get('/members/{username}', [MemberProfileController::class, 'show'])->name('members.show');

// ─── AUTHENTICATION ────────────────────────────────────────
Route::get('/login', fn () => view('web.login'))->name('login')->middleware('guest');
Route::get('/auth/github/redirect', [SocialiteController::class, 'redirect'])->name('auth.github.redirect')->middleware('guest');
Route::get('/auth/github/callback', [SocialiteController::class, 'callback'])->name('auth.github.callback')->middleware('throttle:20,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ─── VIEW AS MEMBER ────────────────────────────────────────
Route::middleware(['auth', 'active'])->group(function () {
    Route::post('/view-as-member/enable', [ViewAsMemberController::class, 'enable'])->name('view-as-member.enable');
    Route::post('/view-as-member/disable', [ViewAsMemberController::class, 'disable'])->name('view-as-member.disable');
});

// ─── AUTHENTICATED ROUTES ──────────────────────────────────
Route::middleware(['auth', 'active'])->group(function () {

    Route::get('/profil/completer', EditProfile::class)->name('profile.edit');
    Route::get('/cv/{userId}', [CvController::class, 'download'])->name('cv.download')->whereNumber('userId');

    Route::middleware('profile.complete')->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'redirect'])->name('dashboard');

        // ─── MEMBER DASHBOARD ──────────────────────────────
        Route::middleware('member.dashboard')
            ->prefix('dashboard/member')
            ->name('dashboard.member.')
            ->group(function () {
                Route::get('/', [MemberDashboardController::class, 'overview'])->name('overview');
                Route::get('/questions', [MemberDashboardController::class, 'questions'])->name('questions');
                Route::get('/articles', [MemberDashboardController::class, 'articles'])->name('articles');
                Route::get('/applications', [MemberDashboardController::class, 'applications'])->name('applications');
                Route::get('/events', fn () => view('dashboard.member.events'))->name('events');
                Route::get('/favorites', fn () => view('dashboard.member.favorites'))->name('favorites');
                Route::get('/profile', fn () => view('dashboard.member.profile'))->name('profile');
                Route::post('/profile', fn () => back())->name('profile.update');
                Route::get('/assistant', [MemberDashboardController::class, 'assistant'])->name('assistant');
            });

        // ─── MODERATOR DASHBOARD ───────────────────────────
        Route::middleware('role:moderator')
            ->prefix('dashboard/moderator')
            ->name('dashboard.moderator.')
            ->group(function () {
                Route::get('/', [ModeratorDashboardController::class, 'overview'])->name('overview');
                Route::get('/reports', [ModeratorDashboardController::class, 'reports'])->name('reports');
                Route::patch('/reports/{report}/resolve', [ModeratorDashboardController::class, 'resolveReport'])->name('reports.resolve');
                Route::patch('/reports/{report}/dismiss', [ModeratorDashboardController::class, 'dismissReport'])->name('reports.dismiss');
                Route::get('/questions', [ModeratorDashboardController::class, 'questions'])->name('questions');
                Route::patch('/questions/{question}/pin', [ModeratorDashboardController::class, 'pinQuestion'])->name('questions.pin');
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

// ─── DESIGN SYSTEM ─────────────────────────────────────────
Route::get('/design-system', [DesignSystemController::class, 'index'])
    ->middleware(['auth', 'role:super-admin|admin']);

// ─── ADMIN — Exports ───────────────────────────────────────
Route::middleware(['auth', 'active', 'role:super-admin|admin'])
    ->prefix('admin-exports')
    ->group(function (): void {
        Route::name('admin.registrations.')->group(function (): void {
            Route::get('/inscriptions/excel', [RegistrationExportController::class, 'excel'])->name('excel');
            Route::get('/inscriptions/pdf', [RegistrationExportController::class, 'pdf'])->name('pdf');
        });
        Route::name('admin.members.')->group(function (): void {
            Route::get('/membres/excel', [MemberExportController::class, 'excel'])->name('excel');
            Route::get('/membres/pdf', [MemberExportController::class, 'pdf'])->name('pdf');
        });
    });

// ═══════════════════════════════════════════════════════════
// ESPACE ENTREPRISE — Guard : company
// ═══════════════════════════════════════════════════════════
Route::prefix('company')->name('company.')->group(function () {

    Route::middleware('company.guest')->group(function () {
        Route::get('/login', [CompanyLoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [CompanyLoginController::class, 'login'])->middleware('throttle:5,1')->name('login.submit');
        Route::get('/register', [CompanyRegisterController::class, 'showRegistrationForm'])->name('register');
        Route::post('/register', [CompanyRegisterController::class, 'store'])->middleware('throttle:3,1')->name('register.submit');
    });

    Route::post('/logout', [CompanyLoginController::class, 'logout'])->name('logout')->middleware('company.auth');

    Route::middleware(['company.auth', 'company.active'])->group(function () {
        Route::get('/password/change', [CompanyPasswordController::class, 'showChangeForm'])->name('password.change');
        Route::post('/password/change', [CompanyPasswordController::class, 'update'])->name('password.update');
    });

    Route::middleware(['company.auth', 'company.active', 'company.password'])->group(function () {
        Route::get('/dashboard', fn () => redirect()->to('/company/portal'))->name('dashboard');
        Route::get('/offers', [CompanyJobOfferController::class, 'index'])->name('offers.index');
        Route::get('/offers/create', [CompanyJobOfferController::class, 'create'])->name('offers.create');
        Route::post('/offers', [CompanyJobOfferController::class, 'store'])->name('offers.store');
        Route::get('/offers/{offer}', [CompanyJobOfferController::class, 'show'])->name('offers.show');
        Route::get('/offers/{offer}/applications', [CompanyApplicationController::class, 'index'])->name('applications.index');
        Route::get('/applications/{application}', [CompanyApplicationController::class, 'show'])->name('applications.show');
        Route::patch('/applications/{application}/status', [CompanyApplicationController::class, 'updateStatus'])->name('applications.status');
        Route::get('/applications/{application}/cv', [CompanyApplicationController::class, 'downloadCv'])->name('applications.cv');
    });
});

// ─── E2E TEST-ONLY LOGIN ───────────────────────────────────
if (app()->environment(['local', 'testing', 'e2e'])) {
    Route::get('/_e2e/login/{email}', function (string $email) {
        $user = \App\Models\User::where('email', $email)->firstOrFail();
        auth()->guard('web')->login($user);

        return redirect('/');
    })->name('e2e.login');

    Route::get('/_e2e/login-company/{email}', function (string $email) {
        $account = \App\Models\CompanyAccount::where('email', $email)->firstOrFail();
        auth()->guard('company')->login($account);

        return redirect('/company/portal');
    })->name('e2e.login-company');
}
