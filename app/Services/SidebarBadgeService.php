<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SidebarBadgeService
{
    private const SECTIONS = [
        'questions'    => 'dashboard.member.questions',
        'articles'     => 'dashboard.member.articles',
        'events'       => 'dashboard.member.events',
        'applications' => 'dashboard.member.applications',
        'mentions'     => 'dashboard.member.mentions',
    ];

    public function memberCounts(User $user, string $currentRoute): array
    {
        $userId = $user->id;

        // Mark the current section as visited now (resets its "new" bubble to 0)
        foreach (self::SECTIONS as $key => $route) {
            if ($currentRoute === $route) {
                $this->markVisited($userId, $key);
                break;
            }
        }

        // Load all last-visit timestamps in one query
        $visits = DB::table('member_section_visits')
            ->where('user_id', $userId)
            ->pluck('visited_at', 'section');

        return [
            'questions'    => $this->questionCounts($userId, $this->toCarbon($visits['questions'] ?? null)),
            'articles'     => $this->articleCounts($userId, $this->toCarbon($visits['articles'] ?? null)),
            'events'       => $this->eventCounts($userId),
            'applications' => $this->applicationCounts($userId, $this->toCarbon($visits['applications'] ?? null)),
            'mentions'     => $this->mentionCounts($userId),
        ];
    }

    public function markVisited(int $userId, string $section): void
    {
        DB::table('member_section_visits')->upsert(
            [['user_id' => $userId, 'section' => $section, 'visited_at' => now()]],
            ['user_id', 'section'],
            ['visited_at']
        );
    }

    private function toCarbon(?string $value): ?Carbon
    {
        return $value ? Carbon::parse($value) : null;
    }

    private function questionCounts(int $userId, ?Carbon $since): array
    {
        $total = DB::table('questions')
            ->where('user_id', $userId)
            ->whereNull('deleted_at')
            ->count();

        $new = 0;
        if ($since) {
            $questionIds = DB::table('questions')
                ->where('user_id', $userId)
                ->whereNull('deleted_at')
                ->pluck('id');

            if ($questionIds->isNotEmpty()) {
                $new = DB::table('answers')
                    ->whereIn('question_id', $questionIds)
                    ->where('created_at', '>', $since)
                    ->count();
            }
        }

        return compact('total', 'new');
    }

    private function articleCounts(int $userId, ?Carbon $since): array
    {
        $total = DB::table('articles')->where('user_id', $userId)->count();

        $new = 0;
        if ($since) {
            $new = DB::table('articles')
                ->where('user_id', $userId)
                ->whereIn('status', ['published', 'rejected'])
                ->where('updated_at', '>', $since)
                ->count();
        }

        return compact('total', 'new');
    }

    private function eventCounts(int $userId): array
    {
        $total = DB::table('event_registrations')
            ->where('user_id', $userId)
            ->whereNotIn('status', ['cancelled'])
            ->count();

        return ['total' => $total, 'new' => 0];
    }

    private function applicationCounts(int $userId, ?Carbon $since): array
    {
        $total = DB::table('job_applications')->where('user_id', $userId)->count();

        $new = 0;
        if ($since) {
            $new = DB::table('job_applications')
                ->where('user_id', $userId)
                ->where('created_at', '>', $since)
                ->count();
        }

        return compact('total', 'new');
    }

    private function mentionCounts(int $userId): array
    {
        $total = DB::table('notifications')
            ->where('notifiable_id', $userId)
            ->where('notifiable_type', 'App\\Models\\User')
            ->where('type', 'like', '%Mention%')
            ->count();

        $new = DB::table('notifications')
            ->where('notifiable_id', $userId)
            ->where('notifiable_type', 'App\\Models\\User')
            ->where('type', 'like', '%Mention%')
            ->whereNull('read_at')
            ->count();

        return compact('total', 'new');
    }
}
