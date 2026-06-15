<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Events;

use App\Http\Requests\Events\CancelEventRegistrationRequest;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

uses(TestCase::class);

function requestWithRegistrationRoute(CancelEventRegistrationRequest $request, EventRegistration $registration): CancelEventRegistrationRequest
{
    $request->setRouteResolver(fn () => new class($registration)
    {
        public function __construct(private EventRegistration $registration) {}

        public function parameter(string $key, $default = null)
        {
            return $key === 'registration' ? $this->registration : $default;
        }
    });

    return $request;
}

it('authorizes the owner of the registration with the cancel permission', function () {
    $user  = makeMember();
    $event = Event::factory()->published()->create(['starts_at' => now()->addWeek(), 'ends_at' => now()->addWeek()->addHours(2)]);

    $registration = EventRegistration::create([
        'event_id'      => $event->id,
        'user_id'       => $user->id,
        'status'        => 'confirmed',
        'ical_token'    => 'token-cancel-1',
        'registered_at' => now(),
    ]);

    $request = new CancelEventRegistrationRequest();
    $request->setUserResolver(fn () => $user);
    requestWithRegistrationRoute($request, $registration);

    expect($request->authorize())->toBeTrue();
});

it('denies a user from cancelling someone else\'s registration', function () {
    $owner = makeMember();
    $other = makeMember();
    $event = Event::factory()->published()->create(['starts_at' => now()->addWeek(), 'ends_at' => now()->addWeek()->addHours(2)]);

    $registration = EventRegistration::create([
        'event_id'      => $event->id,
        'user_id'       => $owner->id,
        'status'        => 'confirmed',
        'ical_token'    => 'token-cancel-2',
        'registered_at' => now(),
    ]);

    $request = new CancelEventRegistrationRequest();
    $request->setUserResolver(fn () => $other);
    requestWithRegistrationRoute($request, $registration);

    expect($request->authorize())->toBeFalse();
});

it('passes validation when the registration can still be cancelled', function () {
    $user  = makeMember();
    $event = Event::factory()->published()->create(['starts_at' => now()->addWeek(), 'ends_at' => now()->addWeek()->addHours(2)]);

    $registration = EventRegistration::create([
        'event_id'      => $event->id,
        'user_id'       => $user->id,
        'status'        => 'confirmed',
        'ical_token'    => 'token-cancel-3',
        'registered_at' => now(),
    ]);

    $request = new CancelEventRegistrationRequest();
    $request->setUserResolver(fn () => $user);
    requestWithRegistrationRoute($request, $registration);

    $validator = Validator::make([], $request->rules(), $request->messages());
    $request->withValidator($validator);

    expect($validator->fails())->toBeFalse();
});

it('rejects cancellation when the event is too close (less than 2 days)', function () {
    $user  = makeMember();
    $event = Event::factory()->published()->create(['starts_at' => now()->addDay(), 'ends_at' => now()->addDay()->addHours(2)]);

    $registration = EventRegistration::create([
        'event_id'      => $event->id,
        'user_id'       => $user->id,
        'status'        => 'confirmed',
        'ical_token'    => 'token-cancel-4',
        'registered_at' => now(),
    ]);

    $request = new CancelEventRegistrationRequest();
    $request->setUserResolver(fn () => $user);
    requestWithRegistrationRoute($request, $registration);

    $validator = Validator::make([], $request->rules(), $request->messages());
    $request->withValidator($validator);

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('registration'))->toBeTrue();
});

it('limits the reason length', function () {
    $request = new CancelEventRegistrationRequest();

    $validator = Validator::make(['reason' => str_repeat('a', 501)], $request->rules(), $request->messages());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('reason'))->toBeTrue();
});
