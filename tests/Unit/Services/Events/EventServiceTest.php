<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Events;

use App\Enums\EventRegistrationStatus;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use App\Services\Events\EventService;
use Tests\TestCase;

uses(TestCase::class);

it('lists published upcoming events ordered by start date', function () {
    Event::factory()->published()->create(['starts_at' => now()->addDays(5), 'ends_at' => now()->addDays(5)->addHours(2)]);
    Event::factory()->published()->create(['starts_at' => now()->addDays(2), 'ends_at' => now()->addDays(2)->addHours(2)]);
    Event::factory()->create(); // draft, should be excluded

    $service = app(EventService::class);
    $results = $service->getEvents();

    expect($results->total())->toBe(2);
    expect($results->first()->starts_at->isBefore($results->last()->starts_at) || $results->first()->starts_at->equalTo($results->last()->starts_at))->toBeTrue();
});

it('retrieves a published event by slug with relations', function () {
    $event = Event::factory()->published()->create();

    $service = app(EventService::class);
    $found   = $service->getBySlug($event->slug);

    expect($found->id)->toBe($event->id);
});

it('lists upcoming events for the homepage', function () {
    Event::factory()->published()->create(['starts_at' => now()->addDay(), 'ends_at' => now()->addDay()->addHours(2)]);
    Event::factory()->past()->create();

    $service = app(EventService::class);
    $upcoming = $service->getUpcomingEvents();

    expect($upcoming)->toHaveCount(1);
});

it('checks whether a user is registered for an event', function () {
    $user  = User::factory()->create();
    $event = Event::factory()->published()->create();

    $service = app(EventService::class);
    expect($service->isRegistered($user, $event))->toBeFalse();

    EventRegistration::create([
        'event_id'      => $event->id,
        'user_id'       => $user->id,
        'status'        => EventRegistrationStatus::Confirmed,
        'ical_token'    => 'token123',
        'registered_at' => now(),
    ]);

    expect($service->isRegistered($user, $event))->toBeTrue();
    expect($service->getRegistration($user, $event))->not->toBeNull();
});

it('reports whether an event is full and the remaining spots', function () {
    $event = Event::factory()->published()->create(['capacity' => 2, 'registrations_count' => 2]);

    $service = app(EventService::class);

    expect($service->isFull($event))->toBeTrue();
    expect($service->spotsLeft($event))->toBe(0);
});
