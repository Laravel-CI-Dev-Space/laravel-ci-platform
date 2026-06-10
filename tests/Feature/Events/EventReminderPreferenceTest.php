<?php

declare(strict_types=1);

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\EventReminder;
use App\Models\User;
use App\Services\Events\EventService;
use Database\Seeders\RoleSeeder;

beforeEach(function () {
    $this->seed(RoleSeeder::class);
});

test('member can enable selected reminders after registration', function () {
    $event = Event::factory()->published()->upcoming()->create(['capacity' => 50]);
    $user  = User::factory()->membreActif()->create();

    EventRegistration::factory()->confirmed()->create([
        'event_id' => $event->id,
        'user_id'  => $user->id,
    ]);

    $service = app(EventService::class);
    $saved   = $service->updateReminderPreferences($event, $user, ['J-7', 'J-1']);

    expect($saved)->toBe(['J-7', 'J-1']);
    $this->assertDatabaseHas('event_registrations', [
        'event_id'       => $event->id,
        'user_id'        => $user->id,
        'reminder_types' => json_encode(['J-7', 'J-1']),
    ]);
    expect(EventReminder::where('event_id', $event->id)->count())->toBeGreaterThan(0);
});

test('member can disable all reminders', function () {
    $event = Event::factory()->published()->upcoming()->create(['capacity' => 50]);
    $user  = User::factory()->membreActif()->create();

    EventRegistration::factory()->confirmed()->withReminders()->create([
        'event_id' => $event->id,
        'user_id'  => $user->id,
    ]);

    $service = app(EventService::class);
    $saved   = $service->updateReminderPreferences($event, $user, []);

    expect($saved)->toBe([]);
    $this->assertDatabaseHas('event_registrations', [
        'event_id'       => $event->id,
        'user_id'        => $user->id,
        'reminder_types' => json_encode([]),
    ]);
});

test('invalid reminder types are ignored', function () {
    $event = Event::factory()->published()->upcoming()->create(['capacity' => 50]);
    $user  = User::factory()->membreActif()->create();

    EventRegistration::factory()->confirmed()->create([
        'event_id' => $event->id,
        'user_id'  => $user->id,
    ]);

    $service = app(EventService::class);
    $saved   = $service->updateReminderPreferences($event, $user, ['J-1', 'invalid']);

    expect($saved)->toBe(['J-1']);
});
