<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Question;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ModeratorDashboardController extends Controller
{
    public function overview(): View
    {
        $pendingArticles  = Article::where('status', 'pending')->with('author')->latest()->take(8)->get();
        $pendingQuestions = Question::where('status', 'hidden')->with('user')->latest()->take(8)->get();
        $newMembers       = User::latest()->take(6)->get();

        // Articles : 3 counts → 1 GROUP BY
        $articleCounts = Article::selectRaw("
                SUM(CASE WHEN status = 'pending'   THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END) as published,
                SUM(CASE WHEN status = 'rejected'  THEN 1 ELSE 0 END) as rejected
            ")
            ->first();

        // Questions : 4 counts → 1 GROUP BY
        $questionCounts = Question::selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'hidden' THEN 1 ELSE 0 END) as hidden,
                SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END) as published_count,
                SUM(CASE WHEN accepted_answer_id IS NOT NULL THEN 1 ELSE 0 END) as answered
            ")
            ->first();

        // Members : 2 counts → 1 requête
        $memberCounts = User::selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as new_week
            ', [now()->subDays(7)])
            ->first();

        $total      = (int) $questionCounts->total;
        $answered   = (int) $questionCounts->answered;

        $stats = [
            'pending_articles'   => (int) $articleCounts->pending,
            'hidden_questions'   => (int) $questionCounts->hidden,
            'total_members'      => (int) $memberCounts->total,
            'new_members_week'   => (int) $memberCounts->new_week,
            'total_questions'    => (int) $questionCounts->published_count,
            'answered_pct'       => $total > 0 ? (int) round(($answered / $total) * 100) : 0,
            'published_articles' => (int) $articleCounts->published,
            'rejected_articles'  => (int) $articleCounts->rejected,
        ];

        return view('dashboard.moderator.overview', compact('stats', 'pendingArticles', 'pendingQuestions', 'newMembers'));
    }

    public function reports(): View
    {
        $reports = Report::with(['reportable', 'reporter'])->latest()->paginate(15);

        // 4 counts → 1 requête
        $row = Report::selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'pending'  THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved_all,
                SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as dismissed
            ")
            ->first();

        $resolvedToday = Report::where('status', 'resolved')
            ->whereDate('handled_at', today())
            ->count();

        $stats = [
            'pending'        => (int) $row->pending,
            'resolved_today' => $resolvedToday,
            'dismissed'      => (int) $row->dismissed,
            'total'          => (int) $row->total,
        ];

        return view('dashboard.moderator.reports', compact('reports', 'stats'));
    }

    public function resolveReport(Report $report): RedirectResponse
    {
        $report->update([
            'status'     => 'resolved',
            'handled_by' => auth()->id(),
            'handled_at' => now(),
        ]);

        return back()->with('success', 'Signalement marqué comme résolu.');
    }

    public function dismissReport(Report $report): RedirectResponse
    {
        $report->update([
            'status'     => 'rejected',
            'handled_by' => auth()->id(),
            'handled_at' => now(),
        ]);

        return back()->with('success', 'Signalement classé sans suite.');
    }

    public function questions(): View
    {
        $questions = Question::with('user')
            ->withCount('reports')
            ->latest()
            ->paginate(15);

        return view('dashboard.moderator.questions', compact('questions'));
    }

    public function pinQuestion(Question $question): RedirectResponse
    {
        $question->update(['is_pinned' => ! $question->is_pinned]);

        return back()->with('success', $question->is_pinned ? 'Question épinglée.' : 'Question désépinglée.');
    }
}
