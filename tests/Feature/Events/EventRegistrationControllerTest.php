<?php

declare(strict_types=1);

use App\Models\Event;
use App\Models\EventRegistration;

it('registers the authenticated member to a published upcoming event', function () {
    $user  = makeMember();
    $event = Event::factory()->published()->create([
        'starts_at' => now()->addWeek(),
        'ends_at'   => now()->addWeek()->addHours(2),
    ]);

    $response = $this->actingAs($user)->post(route('events.register', $event), [
        'event_id' => $event->id,
    ]);

    $response->assertRedirect(route('events.show', $event->slug));
    $response->assertSessionHas('success');
    $this->assertDatabaseHas('event_registrations', [
        'event_id' => $event->id,
        'user_id'  => $user->id,
    ]);
});

it('waitlists the member when the event is full', function () {
    $user  = makeMember();
    $event = Event::factory()->published()->create([
        'starts_at'           => now()->addWeek(),
        'ends_at'             => now()->addWeek()->addHours(2),
        'capacity'            => 1,
        'registrations_count' => 1,
        'waitlist_enabled'    => true,
    ]);

    $response = $this->actingAs($user)->post(route('events.register', $event), [
        'event_id' => $event->id,
    ]);

    $response->assertRedirect(route('events.show', $event->slug));
    $response->assertSessionHas('success');

    $registration = EventRegistration::where('event_id', $event->id)->where('user_id', $user->id)->first();
    expect($registration->status->value)->toBe('waitlisted');
});

it('cancels a registration and redirects back with a success message', function () {
    $user  = makeMember();
    $event = Event::factory()->published()->create([
        'starts_at' => now()->addWeek(),
        'ends_at'   => now()->addWeek()->addHours(2),
    ]);

    $registration = EventRegistration::create([
        'event_id'      => $event->id,
        'user_id'       => $user->id,
        'status'        => 'confirmed',
        'ical_token'    => 'token-cancel-controller',
        'registered_at' => now(),
    ]);

    $response = $this->actingAs($user)
        ->from(route('events.show', $event->slug))
        ->delete(route('events.cancel', $registration));

    $response->assertRedirect(route('events.show', $event->slug));
    $response->assertSessionHas('success');
    expect($registration->refresh()->status->value)->toBe('cancelled');
});

it('downloads the ical file for the owner of the registration', function () {
    $user  = makeMember();
    $event = Event::factory()->published()->create([
        'starts_at' => now()->addWeek(),
        'ends_at'   => now()->addWeek()->addHours(2),
    ]);

    $registration = EventRegistration::create([
        'event_id'      => $event->id,
        'user_id'       => $user->id,
        'status'        => 'confirmed',
        'ical_token'    => 'token-ical-controller',
        'registered_at' => now(),
    ]);

    $response = $this->actingAs($user)->get(route('events.ical', $registration));

    $response->assertOk();
    $response->assertHeader('Content-Type', 'text/calendar; charset=UTF-8');
});

it('denies downloading the ical file for someone else\'s registration', function () {
    $owner = makeMember();
    $other = makeMember();
    $event = Event::factory()->published()->create([
        'starts_at' => now()->addWeek(),
        'ends_at'   => now()->addWeek()->addHours(2),
    ]);

    $registration = EventRegistration::create([
        'event_id'      => $event->id,
        'user_id'       => $owner->id,
        'status'        => 'confirmed',
        'ical_token'    => 'token-ical-controller-2',
        'registered_at' => now(),
    ]);

    $response = $this->actingAs($other)->get(route('events.ical', $registration));

    $response->assertForbidden();
});
