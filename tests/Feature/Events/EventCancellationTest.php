<?php

declare(strict_types=1);

use App\Enums\Events\EventRegistrationStatus;
use App\Enums\Events\EventReminderType;
use App\Mail\EventCancellationMail;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\EventReminder;
use App\Models\EventWaitlist;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    $this->seed(RoleSeeder::class);
});

test('member can cancel event registration', function () {
    Mail::fake();
    $event = Event::factory()->published()->upcoming()->create(['capacity' => 50]);
    $user  = User::factory()->membreActif()->create();

    EventRegistration::factory()->confirmed()->create([
        'event_id' => $event->id,
        'user_id'  => $user->id,
    ]);

    $response = $this->actingAs($user)->post(route('events.cancel', $event));

    $response->assertRedirect(route('events.show', $event));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('event_registrations', [
        'event_id' => $event->id,
        'user_id'  => $user->id,
        'status'   => EventRegistrationStatus::CANCELLED->value,
    ]);

    Mail::assertSent(EventCancellationMail::class);
});

test('cancelled member can register again', function () {
    $event = Event::factory()->published()->upcoming()->create(['capacity' => 50]);
    $user  = User::factory()->membreActif()->create();

    EventRegistration::factory()->create([
        'event_id' => $event->id,
        'user_id'  => $user->id,
        'status'   => EventRegistrationStatus::CANCELLED,
    ]);

    $this->actingAs($user)->post(route('events.register', $event))->assertRedirect();
});

test('registration schedules event reminders when opted in', function () {
    $event = Event::factory()->published()->upcoming()->create(['capacity' => 50]);
    $user  = User::factory()->membreActif()->create();

    $this->actingAs($user)->post(route('events.register', $event), [
        'reminder_types' => ['J-7', 'J-1', 'H-1'],
    ]);

    expect(EventReminder::where('event_id', $event->id)->count())->toBeGreaterThan(0);
    $this->assertDatabaseHas('event_reminders', [
        'event_id' => $event->id,
        'type'     => EventReminderType::J_7->value,
    ]);
    $this->assertDatabaseHas('event_registrations', [
        'event_id'       => $event->id,
        'user_id'        => $user->id,
        'reminder_types' => json_encode(['J-7', 'J-1', 'H-1']),
    ]);
});

test('registration without opt-in does not schedule reminders', function () {
    $event = Event::factory()->published()->upcoming()->create(['capacity' => 50]);
    $user  = User::factory()->membreActif()->create();

    $this->actingAs($user)->post(route('events.register', $event));

    expect(EventReminder::where('event_id', $event->id)->count())->toBe(0);
    $this->assertDatabaseHas('event_registrations', [
        'event_id'       => $event->id,
        'user_id'        => $user->id,
        'reminder_types' => json_encode([]),
    ]);
});

test('registration can opt in to a single reminder type', function () {
    $event = Event::factory()->published()->upcoming()->create(['capacity' => 50]);
    $user  = User::factory()->membreActif()->create();

    $this->actingAs($user)->post(route('events.register', $event), [
        'reminder_types' => ['H-1'],
    ]);

    $this->assertDatabaseHas('event_registrations', [
        'event_id'       => $event->id,
        'user_id'        => $user->id,
        'reminder_types' => json_encode(['H-1']),
    ]);
    $this->assertDatabaseHas('event_reminders', [
        'event_id' => $event->id,
        'type'     => EventReminderType::H_1->value,
    ]);
    $this->assertDatabaseMissing('event_reminders', [
        'event_id' => $event->id,
        'type'     => EventReminderType::J_7->value,
    ]);
});

test('registered member can download calendar ics file', function () {
    $event = Event::factory()->published()->upcoming()->create();
    $user  = User::factory()->membreActif()->create();

    EventRegistration::factory()->confirmed()->create([
        'event_id' => $event->id,
        'user_id'  => $user->id,
    ]);

    $response = $this->actingAs($user)->get(route('events.calendar', $event));

    $response->assertOk();
    $response->assertHeader('content-type', 'text/calendar; charset=utf-8');
    expect($response->streamedContent())->toContain('BEGIN:VCALENDAR');

    $this->assertDatabaseHas('event_ics_exports', [
        'event_id' => $event->id,
        'user_id'  => $user->id,
    ]);
});

test('cancel promotes next person from waitlist', function () {
    $event = Event::factory()->published()->upcoming()->create(['capacity' => 1]);
    $first = User::factory()->membreActif()->create();
    $second = User::factory()->membreActif()->create();

    EventRegistration::factory()->confirmed()->create([
        'event_id' => $event->id,
        'user_id'  => $first->id,
    ]);

    EventWaitlist::create([
        'event_id' => $event->id,
        'user_id'  => $second->id,
        'position' => 1,
    ]);

    $this->actingAs($first)->post(route('events.cancel', $event));

    $this->assertDatabaseHas('event_registrations', [
        'event_id' => $event->id,
        'user_id'  => $second->id,
        'status'   => EventRegistrationStatus::CONFIRMED->value,
    ]);

    $this->assertDatabaseMissing('event_waitlists', [
        'event_id' => $event->id,
        'user_id'  => $second->id,
    ]);
});
