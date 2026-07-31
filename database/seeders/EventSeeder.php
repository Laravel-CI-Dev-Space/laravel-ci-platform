<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $creator = User::where('github_id', '167759591')->first()
            ?? User::first();

        if ($creator === null) {
            $this->command->warn('EventSeeder : aucun utilisateur trouvé, abandon.');

            return;
        }

        // ── Meetup gratuit ─────────────────────────────────────────
        Event::updateOrCreate(
            ['slug' => 'meetup-laravel-ci-abidjan-juillet-2026'],
            [
                'created_by'  => $creator->id,
                'title'       => 'Meetup Laravel CI - Abidjan Juillet 2026',
                'description' => <<<'HTML'
                    <h2>Rejoignez-nous pour notre meetup mensuel !</h2>
                    <p>
                        Ce meetup est l'occasion parfaite pour les développeurs Laravel de Côte d'Ivoire
                        de se retrouver, partager leurs expériences et découvrir les nouveautés de l'écosystème Laravel.
                    </p>
                    <h3>Au programme</h3>
                    <ul>
                        <li>Tour de table des participants</li>
                        <li>Présentation : <strong>Laravel 13 - ce qui change vraiment</strong></li>
                        <li>Retour d'expérience : déploiement Laravel sur infrastructure africaine</li>
                        <li>Open discussion et networking</li>
                    </ul>
                    <p>Entrée <strong>gratuite</strong> - places limitées, inscription obligatoire.</p>
                    HTML,
                'program' => <<<'HTML'
                    <ul>
                        <li><strong>18h00</strong> - Accueil des participants</li>
                        <li><strong>18h30</strong> - Présentation principale (45 min)</li>
                        <li><strong>19h15</strong> - Retour d'expérience (20 min)</li>
                        <li><strong>19h35</strong> - Questions / Réponses</li>
                        <li><strong>20h00</strong> - Networking & Rafraîchissements</li>
                        <li><strong>21h00</strong> - Fin de soirée</li>
                    </ul>
                    HTML,
                'type'                       => 'meetup',
                'location'                   => 'Jokkolabs Abidjan, Rue des Jardins, Cocody',
                'online_url'                 => null,
                'cover_image'                => null,
                'starts_at'                  => '2026-07-15 18:00:00',
                'ends_at'                    => '2026-07-15 21:00:00',
                'capacity'                   => 60,
                'waitlist_enabled'           => true,
                'status'                     => 'published',
                'is_paid'                    => false,
                'price'                      => null,
                'currency'                   => null,
                'promo_code'                 => null,
                'promo_discount_type'        => null,
                'promo_discount_value'       => null,
                'promo_expires_at'           => null,
                'promo_max_uses'             => null,
                'promo_uses_count'           => 0,
                'ticketing_enabled'          => true,
                'ticket_prefix'              => 'LCI',
                'guest_registration_enabled' => true,
                'reminder_7d_sent'           => false,
                'reminder_1d_sent'           => false,
            ]
        );

        // ── Meetup payant avec code promo ──────────────────────────
        Event::updateOrCreate(
            ['slug' => 'workshop-filament-5-abidjan-aout-2026'],
            [
                'created_by'  => $creator->id,
                'title'       => 'Workshop Filament 5 - Abidjan Août 2026',
                'description' => <<<'HTML'
                    <h2>Workshop intensif - Filament 5 de A à Z</h2>
                    <p>
                        Une journée complète pour maîtriser <strong>Filament 5</strong> :
                        ressources, schémas, infolists, actions personnalisées et déploiement en production.
                    </p>
                    <h3>Ce que vous apprendrez</h3>
                    <ul>
                        <li>Architecture de Filament 5 et nouveautés par rapport à la v3</li>
                        <li>Construire un back-office complet de zéro</li>
                        <li>Plugins, relation managers et composants personnalisés</li>
                        <li>Tests unitaires et d'intégration d'un panneau Filament</li>
                        <li>Bonnes pratiques de performance en production</li>
                    </ul>
                    <p>
                        <strong>Places limitées à 25 participants</strong> pour garantir un suivi personnalisé.
                        Code promo <code>EARLYBIRD</code> disponible pour les 10 premiers inscrits : <strong>-30%</strong>.
                    </p>
                    HTML,
                'program' => <<<'HTML'
                    <ul>
                        <li><strong>08h30</strong> - Accueil & café</li>
                        <li><strong>09h00</strong> - Introduction à Filament 5 (architecture, nouveautés)</li>
                        <li><strong>10h30</strong> - Pause</li>
                        <li><strong>10h45</strong> - Atelier : construire une ressource complète</li>
                        <li><strong>13h00</strong> - Déjeuner (inclus)</li>
                        <li><strong>14h00</strong> - Plugins, actions avancées & relation managers</li>
                        <li><strong>16h00</strong> - Pause</li>
                        <li><strong>16h15</strong> - Tests & mise en production</li>
                        <li><strong>17h30</strong> - Q&R et clôture</li>
                    </ul>
                    HTML,
                'type'                       => 'workshop',
                'location'                   => 'Hub Afrique Numérique, Plateau, Abidjan',
                'online_url'                 => null,
                'cover_image'                => null,
                'starts_at'                  => '2026-08-22 08:30:00',
                'ends_at'                    => '2026-08-22 18:00:00',
                'capacity'                   => 25,
                'waitlist_enabled'           => false,
                'status'                     => 'published',
                'is_paid'                    => true,
                'price'                      => 15000.00,
                'currency'                   => 'XOF',
                'promo_code'                 => 'EARLYBIRD',
                'promo_discount_type'        => 'percent',
                'promo_discount_value'       => 30.00,
                'promo_expires_at'           => '2026-08-01 23:59:59',
                'promo_max_uses'             => 10,
                'promo_uses_count'           => 0,
                'ticketing_enabled'          => true,
                'ticket_prefix'              => 'LCI',
                'guest_registration_enabled' => true,
                'reminder_7d_sent'           => false,
                'reminder_1d_sent'           => false,
            ]
        );

        $this->command->info('EventSeeder : 2 événements créés (1 gratuit, 1 payant avec promo).');
    }
}
