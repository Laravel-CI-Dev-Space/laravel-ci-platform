<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\MemberCard;
use App\Models\Profile;
use App\Models\SiteSetting;

class ProfileObserver
{
    /**
     * Surveille les mises à jour des points de réputation.
     * Crée et active les cartes membre lorsque les seuils sont franchis.
     */
    public function updated(Profile $profile): void
    {
        if (! $profile->wasChanged('points')) {
            return;
        }

        $points     = (int) $profile->points;
        $thresholds = $this->thresholds();

        foreach ($thresholds as $level => $required) {
            if ($points < $required) {
                continue;
            }

            $card = MemberCard::firstOrCreate(
                ['user_id' => $profile->user_id, 'level' => $level],
                ['is_active' => false],
            );

            // Active automatiquement si pas encore active et pas forcée par admin
            if (! $card->is_active && ! $card->forced_by_admin) {
                $card->activate();
            }
        }
    }

    /** Lit les seuils depuis SiteSettings, fallback sur config. */
    private function thresholds(): array
    {
        $defaults = config('member-card.thresholds', [1 => 300, 2 => 600, 3 => 900]);

        return [
            1 => (int) (SiteSetting::firstWhere('key', 'card_level_1_points')?->value ?? $defaults[1]),
            2 => (int) (SiteSetting::firstWhere('key', 'card_level_2_points')?->value ?? $defaults[2]),
            3 => (int) (SiteSetting::firstWhere('key', 'card_level_3_points')?->value ?? $defaults[3]),
        ];
    }
}
