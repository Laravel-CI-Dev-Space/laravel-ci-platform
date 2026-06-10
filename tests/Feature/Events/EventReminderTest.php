<?php

declare(strict_types=1);

use App\Enums\Events\EventRegistrationStatus;
use App\Enums\Events\EventReminderType;
use App\Mail\EventCancellationMail;
use App\Mail\EventReminderMail;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\EventReminder;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    $this->seed(RoleSeeder::class);
});

test('cancellation sends email notification to member', function () {
    Mail::fake();

    $event = Event::factory()->published()->upcoming()->create(['capacity' => 50]);
    $user  = User::factory()->membreActif()->create();

    EventRegistration::factory()->confirmed()->create([
        'event_id' => $event->id,
        'user_id'  => $user->id,
    ]);

    $this->actingAs($user)->post(route('events.cancel', $event))->assertRedirect();

    Mail::assertSent(EventCancellationMail::class, function (EventCancellationMail $mail) use ($user, $event) {
        return $mail->hasTo($user->email)
            && $mail->user->is($user)
            && $mail->event->is($event);
    });
});

test('send reminders command emails confirmed registrants and marks reminder sent', function () {
    Mail::fake();

    $event = Event::factory()->published()->upcoming()->create(['capacity' => 50]);
    $user  = User::factory()->membreActif()->create();

    EventRegistration::factory()->confirmed()->withReminders()->create([
        'event_id' => $event->id,
        'user_id'  => $user->id,
    ]);

    $reminder = EventReminder::create([
        'event_id'     => $event->id,
        'type'         => EventReminderType::J_1,
        'scheduled_at' => now()->subMinute(),
    ]);

    $this->artisan('events:send-reminders')->assertSuccessful();

    Mail::assertQueued(EventReminderMail::class, function (EventReminderMail $mail) use ($user, $event) {
        return $mail->hasTo($user->email)
            && $mail->user->is($user)
            && $mail->event->is($event)
            && $mail->reminderType === EventReminderType::J_1;
    });

    expect($reminder->fresh()->sent_at)->not->toBeNull();
});

test('send reminders command skips cancelled registrants', function () {
    Mail::fake();

    $event = Event::factory()->published()->upcoming()->create(['capacity' => 50]);
    $user  = User::factory()->membreActif()->create();

    EventRegistration::factory()->create([
        'event_id' => $event->id,
        'user_id'  => $user->id,
        'status'   => EventRegistrationStatus::CANCELLED,
    ]);

    EventReminder::create([
        'event_id'     => $event->id,
        'type'         => EventReminderType::H_1,
        'scheduled_at' => now()->subMinute(),
    ]);

    $this->artisan('events:send-reminders')->assertSuccessful();

    Mail::assertNothingQueued();
});

test('send reminders command skips registrants who did not opt in', function () {
    Mail::fake();

    $event = Event::factory()->published()->upcoming()->create(['capacity' => 50]);
    $user  = User::factory()->membreActif()->create();

    EventRegistration::factory()->confirmed()->create([
        'event_id' => $event->id,
        'user_id'  => $user->id,
    ]);

    EventReminder::create([
        'event_id'     => $event->id,
        'type'         => EventReminderType::J_7,
        'scheduled_at' => now()->subMinute(),
    ]);

    $this->artisan('events:send-reminders')->assertSuccessful();

    Mail::assertNothingQueued();
});

test('send reminders command only emails registrants who opted in for that type', function () {
    Mail::fake();

    $event = Event::factory()->published()->upcoming()->create(['capacity' => 50]);
    $optedIn = User::factory()->membreActif()->create();
    $optedOut = User::factory()->membreActif()->create();

    EventRegistration::factory()->confirmed()->withReminderTypes(['J-1'])->create([
        'event_id' => $event->id,
        'user_id'  => $optedIn->id,
    ]);

    EventRegistration::factory()->confirmed()->withReminderTypes(['J-7'])->create([
        'event_id' => $event->id,
        'user_id'  => $optedOut->id,
    ]);

    EventReminder::create([
        'event_id'     => $event->id,
        'type'         => EventReminderType::J_1,
        'scheduled_at' => now()->subMinute(),
    ]);

    $this->artisan('events:send-reminders')->assertSuccessful();

    Mail::assertQueued(EventReminderMail::class, 1);
    Mail::assertQueued(EventReminderMail::class, fn (EventReminderMail $mail) => $mail->hasTo($optedIn->email));
});
