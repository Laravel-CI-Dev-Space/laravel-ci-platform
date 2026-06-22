<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Chat\ChatSession;
use App\Models\Chat\ChatTokenBudget;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MemberDashboardController extends Controller
{
    public function overview(): View
    {
        /** @var User $me */
        $me = auth()->user();

        // Compteurs + sum en 1 seule requête
        $stats = DB::table('questions')
            ->where('user_id', $me->id)
            ->whereNull('deleted_at')
            ->selectRaw('COUNT(*) as question_count, COALESCE(SUM(votes_score), 0) as votes_score')
            ->first();

        $answerCount  = DB::table('answers')->where('user_id', $me->id)->count();
        $articleCount = DB::table('articles')
            ->where('user_id', $me->id)
            ->where('status', 'published')
            ->count();

        $recentQuestions = $me->questions()
            ->withCount('answers')
            ->latest('last_activity_at')
            ->take(5)
            ->get();

        $recentArticles = $me->articles()
            ->latest()
            ->take(5)
            ->get();

        $upcomingRegs = $me->eventRegistrations()
            ->with('event')
            ->whereHas('event', fn ($q) => $q->where('starts_at', '>', now()))
            ->latest()
            ->take(3)
            ->get();

        $recentApps = $me->jobApplications()
            ->with(['jobOffer.company'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.member.overview', [
            'me'              => $me,
            'questionCount'   => (int) $stats->question_count,
            'answerCount'     => $answerCount,
            'articleCount'    => $articleCount,
            'votesScore'      => (int) $stats->votes_score,
            'recentQuestions' => $recentQuestions,
            'recentArticles'  => $recentArticles,
            'upcomingRegs'    => $upcomingRegs,
            'recentApps'      => $recentApps,
        ]);
    }

    public function questions(Request $request): View
    {
        /** @var User $user */
        $user   = auth()->user();
        $filter = $request->query('filter');

        $query = $user->questions()->with('tags')->latest();

        match ($filter) {
            'open'     => $query->where('status', 'published')->whereNull('accepted_answer_id'),
            'resolved' => $query->whereNotNull('accepted_answer_id'),
            'hidden'   => $query->where('status', 'hidden'),
            default    => null,
        };

        $questions = $query->paginate(15)->withQueryString();

        // 1 requête au lieu de 4 COUNT() séparées
        $row = DB::table('questions')
            ->where('user_id', $user->id)
            ->whereNull('deleted_at')
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'published' AND accepted_answer_id IS NULL THEN 1 ELSE 0 END) as open,
                SUM(CASE WHEN accepted_answer_id IS NOT NULL THEN 1 ELSE 0 END) as resolved,
                SUM(CASE WHEN status = 'hidden' THEN 1 ELSE 0 END) as hidden
            ")
            ->first();

        $counts = [
            'total'    => (int) $row->total,
            'open'     => (int) $row->open,
            'resolved' => (int) $row->resolved,
            'hidden'   => (int) $row->hidden,
        ];

        return view('dashboard.member.questions', compact('questions', 'counts'));
    }

    public function articles(Request $request): View
    {
        /** @var User $user */
        $user   = auth()->user();
        $status = $request->query('status');
        $valid  = ['draft', 'pending', 'published', 'rejected'];

        $query = $user->articles()->with('tags')->latest();
        if ($status && in_array($status, $valid, true)) {
            $query->where('status', $status);
        }

        $articles = $query->paginate(15)->withQueryString();

        $counts = $user->articles()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        return view('dashboard.member.articles', compact('articles', 'counts'));
    }

    public function applications(): View
    {
        $applications = auth()->user()
            ->jobApplications()
            ->with(['jobOffer.company', 'jobOffer.categories'])
            ->latest()
            ->paginate(15);

        return view('dashboard.member.applications', compact('applications'));
    }

    public function assistant(): View
    {
        /** @var User $user */
        $user = auth()->user();

        $budgetToday = ChatTokenBudget::forToday($user->id);

        $last30 = ChatTokenBudget::where('user_id', $user->id)
            ->where('date', '>=', now()->subDays(29)->toDateString())
            ->orderBy('date')
            ->get();

        $totalPublic    = $last30->sum('public_tokens_used');
        $totalDashboard = $last30->sum('dashboard_tokens_used');
        $totalAll       = $totalPublic + $totalDashboard;

        $sessions = ChatSession::where('user_id', $user->id)
            ->withCount('messages')
            ->latest('last_activity_at')
            ->take(20)
            ->get();

        return view('dashboard.member.assistant', compact(
            'user',
            'budgetToday',
            'last30',
            'totalPublic',
            'totalDashboard',
            'totalAll',
            'sessions',
        ));
    }
}
