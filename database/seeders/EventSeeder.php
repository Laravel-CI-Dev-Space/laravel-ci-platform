<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventSpeaker;
use App\Models\EventType;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $meetup    = EventType::where('slug', 'meetup')->first();
        $webinar   = EventType::where('slug', 'webinar')->first();
        $hackathon = EventType::where('slug', 'hackathon')->first();

        $laravelMeetup = Event::factory()
            ->published()
            ->upcoming()
            ->create([
                'title'       => 'Laravel CI Meetup — Abidjan',
                'slug'        => 'laravel-ci-meetup-abidjan',
                'type_id'     => $meetup?->id,
                'description' => 'Rencontre mensuelle de la communauté Laravel Côte d\'Ivoire.',
                'location'    => 'Abidjan, Cocody',
                'capacity'    => 80,
            ]);

        EventSpeaker::factory()->count(2)->create(['event_id' => $laravelMeetup->id]);

        Event::factory()
            ->published()
            ->upcoming()
            ->webinar()
            ->create([
                'title'   => 'Webinar : Filament v5 en production',
                'type_id' => $webinar?->id,
            ]);

        Event::factory()
            ->published()
            ->upcoming()
            ->create([
                'title'    => 'Hackathon Laravel CI 2026',
                'type_id'  => $hackathon?->id,
                'location' => 'Abidjan, Plateau',
                'capacity' => 120,
            ]);

        Event::factory()
            ->published()
            ->past()
            ->create([
                'title'   => 'Meetup Laravel CI — rétrospective 2025',
                'type_id' => $meetup?->id,
            ]);

        Event::factory()->draft()->create([
            'title'   => 'Atelier API REST (brouillon)',
            'type_id' => $meetup?->id,
        ]);

        Event::factory()->cancelled()->upcoming()->create([
            'title'   => 'Webinar annulé — Déploiement Laravel',
            'type_id' => $webinar?->id,
        ]);

        $this->command?->info('✅ Événements de démo créés.');
    }
}
