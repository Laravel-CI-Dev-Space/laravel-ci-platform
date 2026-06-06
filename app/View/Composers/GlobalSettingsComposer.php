<?php

namespace App\View\Composers;

use App\Models\SiteSetting;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class GlobalSettingsComposer
{
    /** Cache in-request pour éviter N requêtes par vue. */
    private static ?Collection $cache = null;

    public function compose(View $view): void
    {
        $view->with('globalSettings', $this->settings());
    }

    private function settings(): Collection
    {
        if (self::$cache !== null) {
            return self::$cache;
        }

        try {
            self::$cache = SiteSetting::whereIn('group', ['general', 'social', 'identity', 'footer'])
                ->get()
                ->keyBy('key');
        } catch (\Throwable) {
            // Table absente (avant migration) — on renvoie une collection vide
            self::$cache = collect();
        }

        return self::$cache;
    }
}
