<?php

declare(strict_types=1);

use App\Enums\Events\EventRegistrationStatus;
use App\Mail\EventConfirmationMail;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\EventWaitlist;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    $this->seed(RoleSeeder::class);
});

test('guest cannot register for an event', function () {
    $event = Event::factory()->published()->upcoming()->create();

    $this->post(route('events.register', $event))->assertRedirect(route('login'));
});

test('active member can register for an event', function () {
    Mail::fake();

    $event = Event::factory()->published()->upcoming()->create(['capacity' => 50]);
    $user = User::factory()->membreActif()->create();

    $response = $this->actingAs($user)->post(route('events.register', $event));

    $response->assertRedirect(route('events.show', $event));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('event_registrations', [
        'event_id' => $event->id,
        'user_id'  => $user->id,
        'status'   => EventRegistrationStatus::CONFIRMED->value,
    ]);

    Mail::assertSent(EventConfirmationMail::class, function (EventConfirmationMail $mail) use ($user, $event) {
        return $mail->hasTo($user->email)
            && $mail->user->is($user)
            && $mail->event->is($event);
    });
});

test('waitlist registration does not send confirmation email', function () {
    Mail::fake();

    $event = Event::factory()->published()->upcoming()->create(['capacity' => 1]);
    $first = User::factory()->membreActif()->create();
    $second = User::factory()->membreActif()->create();

    EventRegistration::factory()->confirmed()->create([
        'event_id' => $event->id,
        'user_id'  => $first->id,
    ]);

    $this->actingAs($second)->post(route('events.register', $event));

    Mail::assertNothingSent();
});

test('member cannot register twice', function () {
    $event = Event::factory()->published()->upcoming()->create(['capacity' => 50]);
    $user = User::factory()->membreActif()->create();

    $this->actingAs($user)->post(route('events.register', $event));
    $this->actingAs($user)->post(route('events.register', $event))->assertForbidden();
});

test('full event puts member on waitlist', function () {
    $event = Event::factory()->published()->upcoming()->create(['capacity' => 1]);
    $first = User::factory()->membreActif()->create();
    $second = User::factory()->membreActif()->create();

    EventRegistration::factory()->confirmed()->create([
        'event_id' => $event->id,
        'user_id'  => $first->id,
    ]);

    $response = $this->actingAs($second)->post(route('events.register', $event));

    $response->assertRedirect(route('events.show', $event));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('event_waitlists', [
        'event_id' => $event->id,
        'user_id'  => $second->id,
        'position' => 1,
    ]);

    $this->assertDatabaseMissing('event_registrations', [
        'event_id' => $event->id,
        'user_id'  => $second->id,
    ]);
});

test('cannot register for past event', function () {
    $event = Event::factory()->published()->past()->create();
    $user = User::factory()->membreActif()->create();

    $this->actingAs($user)->post(route('events.register', $event))->assertForbidden();
});
