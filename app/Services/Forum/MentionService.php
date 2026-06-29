<?php

declare(strict_types=1);

namespace App\Services\Forum;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class MentionService
{
    private const MAX_MENTIONS = 10;

    private const MENTION_PATTERN = '/@([a-zA-Z0-9_-]{2,39})/';

    /**
     * Extrait les pseudonymes mentionnés (@username) depuis un texte, dédupliqués et limités.
     */
    public function extractUsernames(string $text): Collection
    {
        preg_match_all(self::MENTION_PATTERN, $text, $matches);

        return collect($matches[1] ?? [])
            ->unique()
            ->take(self::MAX_MENTIONS)
            ->values();
    }

    /**
     * Résout les utilisateurs actifs mentionnés dans un texte, en excluant l'auteur.
     */
    public function resolveMentionedUsers(string $text, User $author): Collection
    {
        $usernames = $this->extractUsernames($text);

        if ($usernames->isEmpty()) {
            return collect();
        }

        return User::query()
            ->whereIn('github_username', $usernames->all())
            ->where('is_active', true)
            ->where('id', '!=', $author->id)
            ->get();
    }

    /**
     * Traite les mentions présentes dans un texte et notifie les utilisateurs concernés.
     */
    public function processMentions(string $text, User $author, Model $source): void
    {
        $mentionedUsers = $this->resolveMentionedUsers($text, $author);

        if ($mentionedUsers->isEmpty()) {
            return;
        }

        $notificationService = app(NotificationService::class);

        foreach ($mentionedUsers as $mentionedUser) {
            $notificationService->sendMention($mentionedUser, $author, $source);
        }
    }

    /**
     * Remplace les @username valides par des liens vers le profil du membre, dans un contenu HTML.
     */
    public function renderMentions(string $html): string
    {
        return preg_replace_callback(self::MENTION_PATTERN, function (array $match): string {
            $username = $match[1];

            $user = User::query()
                ->where('github_username', $username)
                ->where('is_active', true)
                ->first();

            if (! $user) {
                return $match[0];
            }

            $url = route('members.show', $username);

            return '<a href="' . $url . '" class="mention">@' . $username . '</a>';
        }, $html);
    }
}
