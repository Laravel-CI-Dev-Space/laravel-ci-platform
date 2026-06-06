<?php

namespace Database\Seeders;

use App\Models\TimelineEvent;
use Illuminate\Database\Seeder;

class TimelineEventSeeder extends Seeder
{
    public function run(): void
    {
        $events = [
            [
                'period'      => 'Jan 2026',
                'title'       => 'Le premier message',
                'description' => 'Une poignée de développeurs abidjanais lancent un groupe WhatsApp pour échanger des conseils Laravel. En une semaine, ils sont 40.',
                'order'       => 1,
            ],
            [
                'period'      => 'Fév 2026',
                'title'       => 'Launch Hack',
                'description' => 'Notre premier hackathon réunit 72 développeurs pour un week-end de construction. La communauté a un rythme cardiaque.',
                'order'       => 2,
            ],
            [
                'period'      => 'Mar 2026',
                'title'       => 'Passage à l\'open source',
                'description' => 'Nous construisons cette plateforme en public sous licence MIT — faite avec Laravel, pour les développeurs Laravel.',
                'order'       => 3,
            ],
            [
                'period'      => 'Mai 2026',
                'title'       => '500 membres',
                'description' => "Sur WhatsApp, LinkedIn et GitHub, nous franchissons les 500 membres — et ce n'est que le début.",
                'order'       => 4,
            ],
        ];

        foreach ($events as $event) {
            TimelineEvent::updateOrCreate(
                ['period' => $event['period']],
                array_merge($event, ['is_active' => true])
            );
        }
    }
}
