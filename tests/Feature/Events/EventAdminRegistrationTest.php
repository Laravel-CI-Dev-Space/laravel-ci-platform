<?php

declare(strict_types=1);

use App\Enums\Events\EventRegistrationStatus;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\EventWaitlist;
use App\Models\User;
use App\Services\Events\EventService;
use Database\Seeders\RoleSeeder;

beforeEach(function () {
    $this->seed(RoleSeeder::class);
});

test('admin can cancel a confirmed registration and promote waitlist', function () {
    $event = Event::factory()->upcoming()->full(capacity: 1)->create();
    $registered = User::factory()->membreActif()->create();
    $waiting    = User::factory()->membreActif()->create();

    EventRegistration::factory()->create([
        'event_id' => $event->id,
        'user_id'  => $registered->id,
        'status'   => EventRegistrationStatus::CONFIRMED,
    ]);

    EventWaitlist::factory()->create([
        'event_id' => $event->id,
        'user_id'  => $waiting->id,
        'position' => 1,
    ]);

    $registration = EventRegistration::query()
        ->where('event_id', $event->id)
        ->where('user_id', $registered->id)
        ->first();

    app(EventService::class)->cancelRegistrationAsAdmin($registration);

    expect($registration->fresh()->status)->toBe(EventRegistrationStatus::CANCELLED);

    $this->assertDatabaseHas('event_registrations', [
        'event_id' => $event->id,
        'user_id'  => $waiting->id,
        'status'   => EventRegistrationStatus::CONFIRMED->value,
    ]);

    $this->assertDatabaseMissing('event_waitlists', [
        'event_id' => $event->id,
        'user_id'  => $waiting->id,
    ]);
});

test('admin can promote a waitlist entry manually', function () {
    $event = Event::factory()->upcoming()->create(['capacity' => 10]);
    $waiting = User::factory()->membreActif()->create();

    $entry = EventWaitlist::factory()->create([
        'event_id' => $event->id,
        'user_id'  => $waiting->id,
        'position' => 1,
    ]);

    app(EventService::class)->promoteWaitlistEntry($entry);

    $this->assertDatabaseHas('event_registrations', [
        'event_id' => $event->id,
        'user_id'  => $waiting->id,
        'status'   => EventRegistrationStatus::CONFIRMED->value,
    ]);

    $this->assertDatabaseMissing('event_waitlists', [
        'event_id' => $event->id,
        'user_id'  => $waiting->id,
    ]);
});

test('admin can remove member from waitlist without promoting', function () {
    $event = Event::factory()->upcoming()->create();
    $waiting = User::factory()->membreActif()->create();

    $entry = EventWaitlist::factory()->create([
        'event_id' => $event->id,
        'user_id'  => $waiting->id,
        'position' => 1,
    ]);

    app(EventService::class)->removeFromWaitlist($entry);

    $this->assertDatabaseMissing('event_waitlists', [
        'event_id' => $event->id,
        'user_id'  => $waiting->id,
    ]);
});
