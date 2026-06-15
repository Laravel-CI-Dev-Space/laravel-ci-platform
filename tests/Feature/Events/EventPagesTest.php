<?php

declare(strict_types=1);

namespace Tests\Feature\Events;

use App\Models\Event;

it('lists upcoming published events on the events index', function () {
    $upcoming = Event::factory()->published()->create(['title' => 'Meetup Laravel Abidjan']);
    Event::factory()->create(['title' => 'Événement brouillon']);

    $response = $this->get(route('events.index'));

    $response->assertOk();
    $response->assertSee($upcoming->title);
    $response->assertDontSee('Événement brouillon');
});

it('shows a published event', function () {
    $event = Event::factory()->published()->create(['title' => 'Hackathon Côte d\'Ivoire']);

    $response = $this->get(route('events.show', $event->slug));

    $response->assertOk();
    $response->assertSee($event->title);
});
