<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Events;

use App\Enums\EventRegistrationStatus;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use App\Services\Events\EventRegistrationService;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    Mail::fake();
});

it('registers a user for a free event and increments the registration count', function () {
    $user  = User::factory()->create();
    $event = Event::factory()->published()->create(['capacity' => 10, 'registrations_count' => 0, 'is_paid' => false]);

    $registration = app(EventRegistrationService::class)->register($user, $event);

    expect($registration->status)->toBe(EventRegistrationStatus::Confirmed);
    expect($registration->payment_status)->toBe('free');
    expect($event->refresh()->registrations_count)->toBe(1);
});

it('waitlists a user when the event is full but waitlist is enabled', function () {
    $user  = User::factory()->create();
    $event = Event::factory()->published()->create([
        'capacity'           => 1,
        'registrations_count' => 1,
        'waitlist_enabled'   => true,
        'is_paid'            => false,
    ]);

    $registration = app(EventRegistrationService::class)->register($user, $event);

    expect($registration->status)->toBe(EventRegistrationStatus::Waitlisted);
    expect($event->refresh()->registrations_count)->toBe(1);
});

it('throws when the event is full and the waitlist is disabled', function () {
    $user  = User::factory()->create();
    $event = Event::factory()->published()->create([
        'capacity'           => 1,
        'registrations_count' => 1,
        'waitlist_enabled'   => false,
        'is_paid'            => false,
    ]);

    expect(fn () => app(EventRegistrationService::class)->register($user, $event))
        ->toThrow(\Exception::class);
});

it('applies a promo code when registering for a paid event', function () {
    $user  = User::factory()->create();
    $event = Event::factory()->published()->create([
        'capacity'             => 10,
        'registrations_count'  => 0,
        'is_paid'              => true,
        'price'                => 100,
        'promo_code'           => 'WELCOME10',
        'promo_discount_type'  => 'percent',
        'promo_discount_value' => 10,
        'promo_uses_count'     => 0,
    ]);

    $registration = app(EventRegistrationService::class)->register($user, $event, 'welcome10');

    expect((float) $registration->amount_paid)->toBe(90.0);
    expect($registration->promo_code_used)->toBe('WELCOME10');
    expect((float) $registration->discount_applied)->toBe(10.0);
    expect($registration->payment_status)->toBe('pending');
    expect($event->refresh()->promo_uses_count)->toBe(1);
});

it('validates a promo code and returns the discounted price', function () {
    $event = Event::factory()->published()->create([
        'is_paid'              => true,
        'price'                => 100,
        'promo_code'           => 'WELCOME10',
        'promo_discount_type'  => 'percent',
        'promo_discount_value' => 10,
    ]);

    $result = app(EventRegistrationService::class)->validatePromo($event, 'welcome10');

    expect($result)->not->toBeNull();
    expect($result['final_price'])->toBe(90.0);

    expect(app(EventRegistrationService::class)->validatePromo($event, 'INVALID'))->toBeNull();
});

it('cancels a confirmed registration, decrements the count and promotes the waitlist', function () {
    $event = Event::factory()->published()->create([
        'capacity'            => 1,
        'registrations_count' => 1,
        'starts_at'           => now()->addDays(10),
        'ends_at'             => now()->addDays(10)->addHours(2),
        'waitlist_enabled'    => true,
        'is_paid'             => false,
    ]);

    $confirmedUser  = User::factory()->create();
    $waitlistedUser = User::factory()->create();

    $confirmed = EventRegistration::create([
        'event_id'      => $event->id,
        'user_id'       => $confirmedUser->id,
        'status'        => EventRegistrationStatus::Confirmed,
        'ical_token'    => 'token-confirmed',
        'registered_at' => now()->subDay(),
    ]);

    $waitlisted = EventRegistration::create([
        'event_id'      => $event->id,
        'user_id'       => $waitlistedUser->id,
        'status'        => EventRegistrationStatus::Waitlisted,
        'ical_token'    => 'token-waitlisted',
        'registered_at' => now(),
    ]);

    app(EventRegistrationService::class)->cancel($confirmed, 'Empêchement');

    expect($confirmed->refresh()->status)->toBe(EventRegistrationStatus::Cancelled);
    expect($waitlisted->refresh()->status)->toBe(EventRegistrationStatus::Confirmed);
    expect($event->refresh()->registrations_count)->toBe(1);
});

it('forbids cancelling a registration too close to the event', function () {
    $event = Event::factory()->published()->create([
        'starts_at' => now()->addHour(),
        'ends_at'   => now()->addHours(3),
    ]);
    $user = User::factory()->create();

    $registration = EventRegistration::create([
        'event_id'      => $event->id,
        'user_id'       => $user->id,
        'status'        => EventRegistrationStatus::Confirmed,
        'ical_token'    => 'token-late',
        'registered_at' => now(),
    ]);

    expect(fn () => app(EventRegistrationService::class)->cancel($registration))
        ->toThrow(\Exception::class);
});

it('generates iCal content for a registration', function () {
    $event = Event::factory()->published()->create();
    $user  = User::factory()->create();

    $registration = EventRegistration::create([
        'event_id'      => $event->id,
        'user_id'       => $user->id,
        'status'        => EventRegistrationStatus::Confirmed,
        'ical_token'    => 'ical-token-123',
        'registered_at' => now(),
    ]);

    $ical = app(EventRegistrationService::class)->generateIcal($registration);

    expect($ical)->toContain('BEGIN:VCALENDAR');
    expect($ical)->toContain('ical-token-123@laravelci.com');
});

it('lists a user\'s registrations with their events', function () {
    $user  = User::factory()->create();
    $event = Event::factory()->published()->create();

    EventRegistration::create([
        'event_id'      => $event->id,
        'user_id'       => $user->id,
        'status'        => EventRegistrationStatus::Confirmed,
        'ical_token'    => 'token-list',
        'registered_at' => now(),
    ]);

    $registrations = app(EventRegistrationService::class)->getUserRegistrations($user);

    expect($registrations)->toHaveCount(1);
    expect($registrations->first()->event->id)->toBe($event->id);
});
