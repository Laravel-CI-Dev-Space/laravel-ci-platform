<?php

namespace App\Services\Web;

use App\Models\AboutOriginSection;
use App\Models\CommunityValue;
use App\Models\Partner;
use App\Models\SiteSetting;
use App\Models\TeamMember;
use App\Models\TimelineEvent;

class AboutService
{
    /** Récupère toutes les données pour la page À propos. */
    public function getAboutData(): array
    {
        return [
            'settings' => SiteSetting::getGroup('about'),
            'origin'   => AboutOriginSection::where('is_active', true)->first(),
            'timeline' => TimelineEvent::active()->get(),
            'team'     => TeamMember::active()->get(),
            'values'   => CommunityValue::active()->get(),
            'partners' => Partner::active()->get(),
        ];
    }
}
