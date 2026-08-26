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
                'title'       => 'Le constat fondateur',
                'description' => "Wilson Kouassi et Mahamadou Diaby cherchent une communauté Laravel en Côte d'Ivoire. Résultat : rien sur LinkedIn, rien sur Facebook, rien sur GitHub. Des centaines de développeurs ivoiriens avancent en silo.",
                'order'       => 1,
            ],
            [
                'period'      => 'Fév 2026',
                'title'       => 'Le premier groupe WhatsApp',
                'description' => "Un groupe WhatsApp est créé. Les développeurs sortent de l'isolement. En un mois, plus de 200 membres rejoignent et les échanges ne s'arrêtent plus.",
                'order'       => 2,
            ],
            [
                'period'      => 'Avr 2026',
                'title'       => 'Construction de la plateforme',
                'description' => "La décision est prise : construire une plateforme open source faite avec Laravel, pour les développeurs Laravel. Forum, blog, événements, emplois : tout ce qu'une communauté sérieuse mérite.",
                'order'       => 3,
            ],
            [
                'period'      => 'Juin 2026',
                'title'       => 'Laravel CI en ligne',
                'description' => "La plateforme est lancée publiquement avec forum, blog, événements et emplois. 5 communautés partenaires. Laravel CI est le point de départ, pas l'arrivée.",
                'order'       => 4,
            ],
            [
                'period'      => 'Août 2026',
                'title'       => '1 100+ membres et le premier talk en présentiel',
                'description' => "Deux mois après le lancement, la communauté dépasse les 1 100 membres. Premier talk en présentiel organisé à Abidjan sur le thème de l'IA au service des développeurs Laravel — une étape décisive dans la vie de la communauté.",
                'order'       => 5,
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
