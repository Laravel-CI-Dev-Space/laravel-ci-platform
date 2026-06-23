<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SidebarBadgeService
{
    private const TTL_DAYS = 60;

    // ── Last-visit cache ─────────────────────────────────────

    public function markVisited(int $userId, string $section): void
    {
        Cache::put(
            $this->cacheKey($userId, $section),
            now()->toDateTimeString(),
            now()->addDays(self::TTL_DAYS)
        );
    }

    public function getLastVisit(int $userId, string $section): ?Carbon
    {
        $raw = Cache::get($this->cacheKey($userId, $section));
        return $raw ? Carbon::parse($raw) : null;
    }

    private function cacheKey(int $userId, string $section): string
    {
        return "sidebar_visit.{$userId}.{$section}";
    }

    // ── Member counts ────────────────────────────────────────

    public function memberCounts(User $user, string $currentRoute): array
    {
        $sections = [
            'questions'    => 'dashboard.member.questions',
            'articles'     => 'dashboard.member.articles',
            'events'       => 'dashboard.member.events',
            'applications' => 'dashboard.member.applications',
            'mentions'     => 'dashboard.member.mentions',
        ];

        // Mark current section as visited before computing (so badge = 0 on current page)
        foreach ($sections as $key => $route) {
            if ($currentRoute === $route) {
                $this->markVisited($user->id, $key);
            }
        }

        $userId = $user->id;

        return [
            'questions' => $this->questionCounts($userId),
            'articles'  => $this->articleCounts($userId),
            'events'    => $this->eventCounts($userId),
            'applications' => $this->applicationCounts($userId, $this->getLastVisit($userId, 'applications')),
            'mentions'  => $this->mentionCounts($userId),
        ];
    }

    private function questionCounts(int $userId): array
    {
        $total = DB::table('questions')->where('user_id', $userId)->whereNull('deleted_at')->count();

        // New = answers received on user's questions since last visit to Questions page
        $since = $this->getLastVisit($userId, 'questions');
        $new   = 0;
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

    private function articleCounts(int $userId): array
    {
        $total = DB::table('articles')->where('user_id', $userId)->count();

        // New = article status changed (published/rejected) since last visit
        $since = $this->getLastVisit($userId, 'articles');
        $new   = 0;
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
