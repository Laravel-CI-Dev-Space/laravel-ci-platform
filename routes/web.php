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
use App\Livewire\EditProfile;
use App\Models\Article;
use App\Models\Question;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Facades\Route;

// ─── PUBLIC WEB PAGES ──────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [AboutController::class, 'index'])->name('about');

// ─── SEARCH ───────────────────────────────────────────────────
Route::get('/search', [SearchController::class, 'index'])->name('search.index');

// ─── FORUM — Routes publiques ──────────────────────────────
Route::prefix('forum')->name('forum.')->group(function () {
    Route::get('/', [QuestionController::class, 'index'])->name('index');

    // Protégé: créer une question (membres + admins/modérateurs disposant de la permission)
    Route::get('/ask', [QuestionController::class, 'create'])
        ->name('ask')
        ->middleware(['auth', 'active', 'profile.complete', 'permission:forum.question.create']);

    // Public: doit venir en dernier
    Route::get('/{slug}', [QuestionController::class, 'show'])->name('show');
});

// ─── FORUM — Routes authentifiées (membres + admins + modérateurs) ──
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

    // Protégé : soumettre un article (membres + admins + modérateurs disposant de la permission)
    Route::get('/submit', [ArticleController::class, 'create'])
        ->name('create')
        ->middleware(['auth', 'active', 'profile.complete', 'permission:blog.article.create']);

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

// ─── BLOG & RESSOURCES — Routes authentifiées ─────────────────────
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
    // Inscription invité (public, pas besoin d'être connecté)
    Route::post('/{event}/guest-register', [GuestRegistrationController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('guest.register');
    // Vérification de ticket (public)
    Route::get('/ticket/{token}', fn (string $token) => view('web.events.ticket-verify', compact('token')))
        ->name('ticket.verify');
});

// ─── EVENTS — Routes authentifiées ────────────────────────
Route::middleware(['auth', 'active', 'profile.complete'])->group(function () {
    Route::post('/events/{event}/register',
        [EventRegistrationController::class, 'store']
    )->name('events.register');

    Route::delete('/events/registrations/{registration}',
        [EventRegistrationController::class, 'destroy']
    )->name('events.cancel');

    Route::get('/events/registrations/{registration}/ical',
        [EventRegistrationController::class, 'downloadIcal']
    )->name('events.ical');
});

// ─── JOB BOARD — Routes publiques ────────────────────────────
Route::prefix('jobs')->name('jobs.')->group(function () {
    Route::get('/', [JobOfferController::class, 'index'])->name('index');
    Route::get('/{slug}', [JobOfferController::class, 'show'])->name('show');
});

// ─── JOB BOARD — Routes authentifiées ─────────────────────────────
Route::middleware(['auth', 'active', 'profile.complete'])->group(function () {
    Route::post('/jobs/{offer}/apply', [JobApplicationController::class, 'store'])
        ->middleware(['throttle:10,1', 'permission:job.apply'])
        ->name('jobs.applications.store');
    Route::post('/jobs/{offer}/favorite', [JobOfferController::class, 'toggleFavorite'])
        ->middleware('permission:job.favorite')
        ->name('jobs.favorite');
});

// ─── MEMBER PUBLIC PROFILE ─────────────────────────────────
Route::get('/members/{username}', function (string $username) {
    $member = User::where('github_username', $username)
        ->with('profile.grade')
        ->withCount(['questions', 'answers', 'articles'])
        ->firstOrFail();

    if ($member->profile) {
        $member->bio        = $member->profile->bio;
        $member->location   = collect([$member->profile->city, $member->profile->country])->filter()->implode(', ');
        $member->skills     = $member->profile->tech_stack;
        $member->reputation = $member->profile->points;
        $member->grade      = $member->profile->grade;
    }

    $recentQuestions = $member->questions()->with('tags')->latest()->take(5)->get();
    $recentArticles  = $member->articles()->where('status', 'published')->latest()->take(5)->get();

    return view('web.members.show', compact('member', 'recentQuestions', 'recentArticles'));
})->name('members.show');

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

// ─── VIEW AS MEMBER — Admin/Moderator only ────────────────────
Route::middleware(['auth', 'active'])->group(function () {
    Route::post('/view-as-member/enable', [ViewAsMemberController::class, 'enable'])
        ->name('view-as-member.enable');
    Route::post('/view-as-member/disable', [ViewAsMemberController::class, 'disable'])
        ->name('view-as-member.disable');
});

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
        Route::middleware('member.dashboard')
            ->prefix('dashboard/member')
            ->name('dashboard.member.')
            ->group(function () {
                Route::get('/', fn () => view('dashboard.member.overview'))->name('overview');
                Route::get('/questions', function () {
                    $user   = auth()->user();
                    $filter = request('filter'); // open | resolved | hidden

                    $query = $user->questions()->with('tags')->latest();

                    if ($filter === 'open') {
                        $query->where('status', 'published')->whereNull('accepted_answer_id');
                    } elseif ($filter === 'resolved') {
                        $query->whereNotNull('accepted_answer_id');
                    } elseif ($filter === 'hidden') {
                        $query->where('status', 'hidden');
                    }

                    $questions = $query->paginate(15)->withQueryString();

                    $counts = [
                        'total'    => $user->questions()->count(),
                        'open'     => $user->questions()->where('status', 'published')->whereNull('accepted_answer_id')->count(),
                        'resolved' => $user->questions()->whereNotNull('accepted_answer_id')->count(),
                        'hidden'   => $user->questions()->where('status', 'hidden')->count(),
                    ];

                    return view('dashboard.member.questions', compact('questions', 'counts'));
                })->name('questions');
                Route::get('/articles', function () {
                    $user   = auth()->user();
                    $status = request('status');
                    $valid  = ['draft', 'pending', 'published', 'rejected'];

                    $query = $user->articles()->with('tags')->latest();
                    if ($status && in_array($status, $valid)) {
                        $query->where('status', $status);
                    }

                    $articles = $query->paginate(15)->withQueryString();

                    $counts = $user->articles()
                        ->selectRaw('status, count(*) as total')
                        ->groupBy('status')
                        ->pluck('total', 'status')
                        ->toArray();

                    return view('dashboard.member.articles', compact('articles', 'counts'));
                })->name('articles');
                Route::get('/events', fn () => view('dashboard.member.events'))->name('events');
                Route::get('/applications', function () {
                    $applications = auth()->user()
                        ->jobApplications()
                        ->with(['jobOffer.company', 'jobOffer.categories'])
                        ->latest()
                        ->paginate(15);

                    return view('dashboard.member.applications', compact('applications'));
                })->name('applications');
                Route::get('/favorites', fn () => view('dashboard.member.favorites'))->name('favorites');
                Route::get('/profile', fn () => view('dashboard.member.profile'))->name('profile');
                Route::post('/profile', fn () => back())->name('profile.update');
            });

        // ─── MODERATOR DASHBOARD ───────────────────────────
        Route::middleware('role:moderator')
            ->prefix('dashboard/moderator')
            ->name('dashboard.moderator.')
            ->group(function () {
                Route::get('/', function () {
                    $pendingArticles  = Article::where('status', 'pending')->with('author')->latest()->take(8)->get();
                    $pendingQuestions = Question::where('status', 'hidden')->with('user')->latest()->take(8)->get();
                    $newMembers       = User::latest()->take(6)->get();

                    $stats = [
                        'pending_articles' => Article::where('status', 'pending')->count(),
                        'hidden_questions' => Question::where('status', 'hidden')->count(),
                        'total_members'    => User::count(),
                        'new_members_week' => User::where('created_at', '>=', now()->subDays(7))->count(),
                        'total_questions'  => Question::where('status', 'published')->count(),
                        'answered_pct'     => Question::count() > 0
                            ? round((Question::whereNotNull('accepted_answer_id')->count() / Question::count()) * 100)
                            : 0,
                        'published_articles' => Article::where('status', 'published')->count(),
                        'rejected_articles'  => Article::where('status', 'rejected')->count(),
                    ];

                    return view('dashboard.moderator.overview', compact('stats', 'pendingArticles', 'pendingQuestions', 'newMembers'));
                })->name('overview');
                Route::get('/reports', function () {
                    $reports = Report::with(['reportable', 'reporter'])->latest()->paginate(15);

                    $stats = [
                        'pending'        => Report::where('status', 'pending')->count(),
                        'resolved_today' => Report::where('status', 'resolved')->whereDate('handled_at', today())->count(),
                        'dismissed'      => Report::where('status', 'rejected')->count(),
                        'total'          => Report::count(),
                    ];

                    return view('dashboard.moderator.reports', compact('reports', 'stats'));
                })->name('reports');
                Route::patch('/reports/{report}/resolve', function (Report $report) {
                    $report->update([
                        'status'     => 'resolved',
                        'handled_by' => auth()->id(),
                        'handled_at' => now(),
                    ]);

                    return back()->with('success', 'Signalement marqué comme résolu.');
                })->name('reports.resolve');
                Route::patch('/reports/{report}/dismiss', function (Report $report) {
                    $report->update([
                        'status'     => 'rejected',
                        'handled_by' => auth()->id(),
                        'handled_at' => now(),
                    ]);

                    return back()->with('success', 'Signalement classé sans suite.');
                })->name('reports.dismiss');
                Route::get('/questions', function () {
                    $questions = Question::with('user')
                        ->withCount('reports')
                        ->latest()
                        ->paginate(15);

                    return view('dashboard.moderator.questions', compact('questions'));
                })->name('questions');
                Route::patch('/questions/{question}/pin', function (Question $question) {
                    $question->update(['is_pinned' => ! $question->is_pinned]);

                    return back()->with('success', $question->is_pinned ? 'Question épinglée.' : 'Question désépinglée.');
                })->name('questions.pin');
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

// ─── ADMIN — Exports inscriptions ──────────────────────────
Route::middleware(['auth', 'active', 'role:super-admin|admin'])
    ->prefix('admin-exports')
    ->group(function (): void {
        Route::name('admin.registrations.')
            ->group(function (): void {
                Route::get('/inscriptions/excel', [RegistrationExportController::class, 'excel'])->name('excel');
                Route::get('/inscriptions/pdf', [RegistrationExportController::class, 'pdf'])->name('pdf');
            });

        Route::name('admin.members.')
            ->group(function (): void {
                Route::get('/membres/excel', [MemberExportController::class, 'excel'])->name('excel');
                Route::get('/membres/pdf', [MemberExportController::class, 'pdf'])->name('pdf');
            });
    });

// ═══════════════════════════════════════════════════════════
// ESPACE ENTREPRISE — Guard : company
// ═══════════════════════════════════════════════════════════
Route::prefix('company')->name('company.')->group(function () {

    // ─── Auth entreprise (invités uniquement) ───────────
    // company.guest redirige vers company.dashboard (pas vers HOME web)
    Route::middleware('company.guest')->group(function () {
        Route::get('/login', [CompanyLoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [CompanyLoginController::class, 'login'])
            ->middleware('throttle:5,1')
            ->name('login.submit');
        Route::get('/register', [CompanyRegisterController::class, 'showRegistrationForm'])->name('register');
        Route::post('/register', [CompanyRegisterController::class, 'store'])
            ->middleware('throttle:3,1')
            ->name('register.submit');
    });

    // Déconnexion
    Route::post('/logout', [CompanyLoginController::class, 'logout'])
        ->name('logout')
        ->middleware('company.auth');

    // Changement de mot de passe obligatoire (auth + compte actif)
    Route::middleware(['company.auth', 'company.active'])->group(function () {
        Route::get('/password/change', [CompanyPasswordController::class, 'showChangeForm'])->name('password.change');
        Route::post('/password/change', [CompanyPasswordController::class, 'update'])->name('password.update');
    });

    // ─── Dashboard entreprise (auth + actif + mdp changé) ─
    Route::middleware(['company.auth', 'company.active', 'company.password'])->group(function () {
        // Le dashboard est désormais le panel Filament company (/company/portal)
        Route::get('/dashboard', fn () => redirect()->to('/company/portal'))->name('dashboard');

        // Offres
        Route::get('/offers', [CompanyJobOfferController::class, 'index'])->name('offers.index');
        Route::get('/offers/create', [CompanyJobOfferController::class, 'create'])->name('offers.create');
        Route::post('/offers', [CompanyJobOfferController::class, 'store'])->name('offers.store');
        Route::get('/offers/{offer}', [CompanyJobOfferController::class, 'show'])->name('offers.show');

        // Candidatures
        Route::get('/offers/{offer}/applications', [CompanyApplicationController::class, 'index'])->name('applications.index');
        Route::get('/applications/{application}', [CompanyApplicationController::class, 'show'])->name('applications.show');
        Route::patch('/applications/{application}/status', [CompanyApplicationController::class, 'updateStatus'])->name('applications.status');
        Route::get('/applications/{application}/cv', [CompanyApplicationController::class, 'downloadCv'])->name('applications.cv');
    });
});
