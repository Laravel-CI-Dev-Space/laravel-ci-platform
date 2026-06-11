<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\UserRole;
use App\Models\Answer;
use App\Models\Article;
use App\Models\Comment;
use App\Models\EventRegistration;
use App\Models\Grade;
use App\Models\Profile;
use App\Models\Question;
use App\Models\SiteSetting;
use App\Models\User;

/**
 * Calcule les points de réputation d'un membre à partir de ses interactions
 * (questions, réponses, articles, votes reçus, commentaires, événements,
 * ancienneté) et lui attribue le grade correspondant.
 */
class ReputationService
{
    private const POINTS_PER_QUESTION = 5;

    private const POINTS_PER_ANSWER = 10;

    private const POINTS_PER_ACCEPTED_ANSWER = 20;

    private const POINTS_PER_PUBLISHED_ARTICLE = 25;

    private const POINTS_PER_VOTE = 2;

    private const POINTS_PER_COMMENT = 1;

    private const POINTS_PER_EVENT_PARTICIPATION = 5;

    private const POINTS_PER_SENIORITY_MONTH = 2;

    private const MAX_SENIORITY_MONTHS = 25;

    /**
     * Calcule le total de points de réputation d'un membre.
     */
    public function calculatePoints(User $user): int
    {
        $pointsPerQuestion       = $this->setting('points_question', self::POINTS_PER_QUESTION);
        $pointsPerAnswer         = $this->setting('points_answer', self::POINTS_PER_ANSWER);
        $pointsPerAcceptedAnswer = $this->setting('points_accepted_answer', self::POINTS_PER_ACCEPTED_ANSWER);
        $pointsPerArticle        = $this->setting('points_article', self::POINTS_PER_PUBLISHED_ARTICLE);
        $pointsPerVote           = $this->setting('points_vote', self::POINTS_PER_VOTE);
        $pointsPerComment        = $this->setting('points_comment', self::POINTS_PER_COMMENT);
        $pointsPerEvent          = $this->setting('points_event', self::POINTS_PER_EVENT_PARTICIPATION);

        $points = 0;

        $points += Question::where('user_id', $user->id)->where('status', 'published')->count() * $pointsPerQuestion;

        $answers = Answer::where('user_id', $user->id)->get(['is_accepted', 'votes_score']);
        $points += $answers->count()                             * $pointsPerAnswer;
        $points += $answers->where('is_accepted', true)->count() * $pointsPerAcceptedAnswer;
        $points += (int) $answers->sum('votes_score')            * $pointsPerVote;

        $points += Article::where('user_id', $user->id)->where('status', 'published')->count() * $pointsPerArticle;

        $points += (int) Question::where('user_id', $user->id)->sum('votes_score') * $pointsPerVote;

        $points += Comment::where('user_id', $user->id)->count() * $pointsPerComment;

        $points += EventRegistration::where('user_id', $user->id)
            ->whereIn('status', ['confirmed', 'attended'])
            ->count() * $pointsPerEvent;

        $points += $this->seniorityPoints($user);

        return max(0, $points);
    }

    /**
     * Détermine le grade correspondant à un total de points.
     *
     * Le super-administrateur reçoit toujours le grade le plus élevé.
     */
    public function determineGrade(User $user, int $points): ?Grade
    {
        if ($user->hasRole(UserRole::SuperAdmin->value)) {
            return Grade::highest();
        }

        return Grade::forPoints($points);
    }

    /**
     * Recalcule et persiste les points et le grade d'un membre.
     */
    public function recalculate(User $user): Profile
    {
        $points = $this->calculatePoints($user);
        $grade  = $this->determineGrade($user, $points);

        $profile           = $user->profile ?? $user->profile()->make(['user_id' => $user->id]);
        $profile->points   = $points;
        $profile->grade_id = $grade?->id;
        $profile->save();

        return $profile;
    }

    /**
     * Recalcule les points et grades de tous les membres ayant un profil.
     */
    public function recalculateAll(): void
    {
        User::whereHas('profile')->each(fn (User $user) => $this->recalculate($user));
    }

    /**
     * Points liés à l'ancienneté du compte (plafonnés).
     */
    private function seniorityPoints(User $user): int
    {
        $months = (int) $user->created_at?->diffInMonths(now());
        $months = min($months, $this->setting('max_seniority_months', self::MAX_SENIORITY_MONTHS));

        return $months * $this->setting('points_seniority_month', self::POINTS_PER_SENIORITY_MONTH);
    }

    /**
     * Récupère un paramètre de réputation configurable depuis le panneau d'administration.
     */
    private function setting(string $key, int $default): int
    {
        return (int) SiteSetting::get('reputation_' . $key, $default);
    }
}
