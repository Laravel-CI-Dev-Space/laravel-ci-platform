<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Chat\ChatTokenBudget;
use App\Models\Chat\PlatformDailyStat;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PlatformDailyStatsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly Carbon $date = new Carbon('today')) {}

    public function handle(): void
    {
        $day   = $this->date->toDateString();
        $start = Carbon::parse($day)->startOfDay();
        $end   = Carbon::parse($day)->endOfDay();

        $newUsers = DB::table('users')
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $totalUsers = DB::table('users')->count();

        $activeUsers = DB::table('users')
            ->whereBetween('last_login_at', [$start, $end])
            ->count();

        $newQuestions = DB::table('questions')
            ->whereNull('deleted_at')
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $newAnswers = DB::table('answers')
            ->whereNull('deleted_at')
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $newArticles = DB::table('articles')
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $newComments = DB::table('comments')
            ->whereNull('deleted_at')
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $newJobOffers = DB::table('job_offers')
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $newApplications = DB::table('job_applications')
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $newRegistrations = DB::table('event_registrations')
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $chatRow = ChatTokenBudget::whereDate('date', $day)
            ->selectRaw('
                COALESCE(SUM(public_tokens_used + dashboard_tokens_used), 0) as tokens,
                COUNT(DISTINCT user_id) as sessions
            ')
            ->first();

        PlatformDailyStat::updateOrCreate(
            ['date' => $day],
            [
                'new_users'          => $newUsers,
                'active_users'       => $activeUsers,
                'total_users'        => $totalUsers,
                'new_questions'      => $newQuestions,
                'new_answers'        => $newAnswers,
                'new_articles'       => $newArticles,
                'new_comments'       => $newComments,
                'new_job_offers'     => $newJobOffers,
                'new_applications'   => $newApplications,
                'new_registrations'  => $newRegistrations,
                'total_chat_tokens'  => (int) ($chatRow->tokens ?? 0),
                'total_chat_sessions'=> (int) ($chatRow->sessions ?? 0),
            ]
        );
    }
}
