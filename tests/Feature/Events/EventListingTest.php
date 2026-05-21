<?php

declare(strict_types=1);

use App\Enums\Events\EventStatus;
use App\Models\Event;
use App\Models\EventType;
use App\Models\User;

test('guest can browse published upcoming events', function () {
    $type = EventType::factory()->webinar()->create();

    $visible = Event::factory()->published()->upcoming()->create([
        'title'   => 'Meetup Laravel visible',
        'type_id' => $type->id,
    ]);

    Event::factory()->draft()->create([
        'title'      => 'Brouillon secret',
        'start_date' => now()->addWeek(),
        'end_date'   => now()->addWeek()->addHours(3),
    ]);
    Event::factory()->published()->past()->create(['title' => 'Event passé']);

    $response = $this->get(route('events.index'));

    $response->assertOk();
    $response->assertSee($visible->title);
    $response->assertDontSee('Brouillon secret');
});

test('guest can filter events by type', function () {
    $webinar = EventType::factory()->webinar()->create();
    $meetup = EventType::factory()->meetup()->create();

    Event::factory()->published()->upcoming()->create([
        'title'   => 'Webinar Filament',
        'type_id' => $webinar->id,
    ]);

    Event::factory()->published()->upcoming()->create([
        'title'   => 'Meetup Abidjan',
        'type_id' => $meetup->id,
    ]);

    $response = $this->get(route('events.index', ['type' => 'webinar']));

    $response->assertOk();
    $response->assertSee('Webinar Filament');
    $response->assertDontSee('Meetup Abidjan');
});

test('guest can view a published event detail page', function () {
    $event = Event::factory()->published()->upcoming()->create([
        'title' => 'Détail event public',
    ]);

    $response = $this->get(route('events.show', $event));

    $response->assertOk();
    $response->assertSee('Détail event public');
});

test('guest cannot view a draft event', function () {
    $event = Event::factory()->draft()->create();

    $this->get(route('events.show', $event))->assertForbidden();
});
