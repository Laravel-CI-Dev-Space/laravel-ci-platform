<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Events;

use App\Http\Requests\Events\StoreEventRegistrationRequest;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

uses(TestCase::class);

function requestWithEventRoute(StoreEventRegistrationRequest $request, Event $event): StoreEventRegistrationRequest
{
    $request->setRouteResolver(fn () => new class($event)
    {
        public function __construct(private Event $event) {}

        public function parameter(string $key, $default = null)
        {
            return $key === 'event' ? $this->event : $default;
        }
    });

    return $request;
}

it('authorizes an active member with the event register permission', function () {
    $user    = makeMember();
    $request = new StoreEventRegistrationRequest();
    $request->setUserResolver(fn () => $user);

    expect($request->authorize())->toBeTrue();
});

it('denies a guest from registering', function () {
    $request = new StoreEventRegistrationRequest();
    $request->setUserResolver(fn () => null);

    expect($request->authorize())->toBeFalse();
});

it('passes validation for a published, upcoming, open event', function () {
    $user  = makeMember();
    $event = Event::factory()->published()->create(['starts_at' => now()->addWeek(), 'ends_at' => now()->addWeek()->addHours(2)]);

    $request = new StoreEventRegistrationRequest();
    $request->setUserResolver(fn () => $user);
    requestWithEventRoute($request, $event);

    $validator = Validator::make(['event_id' => $event->id], $request->rules(), $request->messages());
    $request->withValidator($validator);

    expect($validator->fails())->toBeFalse();
});

it('rejects registration for a non-published event', function () {
    $user  = makeMember();
    $event = Event::factory()->create(['starts_at' => now()->addWeek(), 'ends_at' => now()->addWeek()->addHours(2)]);

    $request = new StoreEventRegistrationRequest();
    $request->setUserResolver(fn () => $user);
    requestWithEventRoute($request, $event);

    $validator = Validator::make(['event_id' => $event->id], $request->rules(), $request->messages());
    $request->withValidator($validator);

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('event_id'))->toBeTrue();
});

it('rejects registration for a past event', function () {
    $user  = makeMember();
    $event = Event::factory()->past()->create();

    $request = new StoreEventRegistrationRequest();
    $request->setUserResolver(fn () => $user);
    requestWithEventRoute($request, $event);

    $validator = Validator::make(['event_id' => $event->id], $request->rules(), $request->messages());
    $request->withValidator($validator);

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('event_id'))->toBeTrue();
});

it('rejects registration when the user is already registered', function () {
    $user  = makeMember();
    $event = Event::factory()->published()->create(['starts_at' => now()->addWeek(), 'ends_at' => now()->addWeek()->addHours(2)]);

    EventRegistration::create([
        'event_id'      => $event->id,
        'user_id'       => $user->id,
        'status'        => 'confirmed',
        'ical_token'    => 'token-already',
        'registered_at' => now(),
    ]);

    $request = new StoreEventRegistrationRequest();
    $request->setUserResolver(fn () => $user);
    requestWithEventRoute($request, $event);

    $validator = Validator::make(['event_id' => $event->id], $request->rules(), $request->messages());
    $request->withValidator($validator);

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('event_id'))->toBeTrue();
});

it('rejects registration when the event is full without a waitlist', function () {
    $user  = makeMember();
    $event = Event::factory()->published()->create([
        'starts_at'           => now()->addWeek(),
        'ends_at'             => now()->addWeek()->addHours(2),
        'capacity'            => 1,
        'registrations_count' => 1,
        'waitlist_enabled'    => false,
    ]);

    $request = new StoreEventRegistrationRequest();
    $request->setUserResolver(fn () => $user);
    requestWithEventRoute($request, $event);

    $validator = Validator::make(['event_id' => $event->id], $request->rules(), $request->messages());
    $request->withValidator($validator);

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('event_id'))->toBeTrue();
});
