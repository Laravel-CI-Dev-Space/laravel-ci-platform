<?php

declare(strict_types=1);

namespace App\Services\Forum;

use App\Models\User;
use App\Models\Vote;
use Illuminate\Database\Eloquent\Model;

class VoteService
{
    /**
     * Vote ou retire le vote sur une entité (question ou réponse).
     * Si l'user a déjà voté avec la même valeur, retire le vote.
     * Si l'user a voté différemment, change le vote.
     * Met à jour votes_score sur le modèle votable.
     *
     * @param  Model  $votable  — Question or Answer instance
     * @param  int  $value  — 1 (upvote) or -1 (downvote)
     * @return array{score: int, userVote: int|null}
     */
    public function vote(User $user, Model $votable, int $value): array
    {
        $existing = Vote::where([
            'votable_id'   => $votable->id,
            'votable_type' => $votable->getMorphClass(),
            'user_id'      => $user->id,
        ])->first();

        if ($existing !== null) {
            if ($existing->value === $value) {
                // Retrait du vote (même valeur)
                $delta    = -$existing->value;
                $userVote = null;
                $existing->delete();
            } else {
                // Changement de vote (valeur opposée)
                $delta    = $value - $existing->value;
                $userVote = $value;
                $existing->update(['value' => $value]);
            }
        } else {
            // Nouveau vote
            $delta    = $value;
            $userVote = $value;

            Vote::create([
                'votable_id'   => $votable->id,
                'votable_type' => $votable->getMorphClass(),
                'user_id'      => $user->id,
                'value'        => $value,
            ]);
        }

        $votable->increment('votes_score', $delta);
        $votable->refresh();

        return [
            'score'    => $votable->votes_score,
            'userVote' => $userVote,
        ];
    }

    /**
     * Retourne le vote actuel de l'user sur une entité.
     *
     * @return int|null — 1, -1, or null
     */
    public function getUserVote(User $user, Model $votable): ?int
    {
        return Vote::where([
            'votable_id'   => $votable->id,
            'votable_type' => $votable->getMorphClass(),
            'user_id'      => $user->id,
        ])->value('value');
    }
}
