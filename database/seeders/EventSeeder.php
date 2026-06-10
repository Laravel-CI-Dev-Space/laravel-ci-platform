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

        $this->seedBulkEvents($meetup, $webinar, $hackathon);

        $total = Event::count();
        $this->command?->info("✅ Événements de démo créés ({$total} au total).");
    }

    private function seedBulkEvents(?EventType $meetup, ?EventType $webinar, ?EventType $hackathon): void
    {
        if ($meetup) {
            Event::factory()
                ->published()
                ->upcoming()
                ->count(10)
                ->create(['type_id' => $meetup->id])
                ->each(fn (Event $event) => $this->maybeSeedSpeakers($event));
        }

        if ($webinar) {
            Event::factory()
                ->published()
                ->upcoming()
                ->webinar()
                ->count(8)
                ->create(['type_id' => $webinar->id])
                ->each(fn (Event $event) => $this->maybeSeedSpeakers($event, 80));
        }

        if ($hackathon) {
            Event::factory()
                ->published()
                ->upcoming()
                ->count(6)
                ->create([
                    'type_id'  => $hackathon->id,
                    'location' => fake()->randomElement(['Abidjan, Plateau', 'Abidjan, Cocody', 'Bouaké']),
                    'capacity' => fake()->numberBetween(60, 200),
                ])
                ->each(fn (Event $event) => $this->maybeSeedSpeakers($event, 90));
        }

        $pastTypes = collect([$meetup, $webinar, $hackathon])->filter();

        foreach ($pastTypes as $type) {
            $factory = Event::factory()->published()->past()->count(5);

            if ($type->slug === 'webinar') {
                $factory = $factory->webinar();
            }

            $factory->create(['type_id' => $type->id])
                ->each(fn (Event $event) => $this->maybeSeedSpeakers($event, 50));
        }

        Event::factory()
            ->draft()
            ->count(6)
            ->create(['type_id' => $meetup?->id])
            ->each(fn (Event $event) => $this->maybeSeedSpeakers($event, 30));

        Event::factory()
            ->cancelled()
            ->upcoming()
            ->count(4)
            ->create(['type_id' => $webinar?->id ?? $meetup?->id]);
    }

    private function maybeSeedSpeakers(Event $event, int $chancePercent = 70): void
    {
        if (! fake()->boolean($chancePercent)) {
            return;
        }

        EventSpeaker::factory()
            ->count(fake()->numberBetween(1, 3))
            ->create(['event_id' => $event->id]);
    }
}
