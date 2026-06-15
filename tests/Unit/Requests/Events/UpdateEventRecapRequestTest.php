<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Events;

use App\Http\Requests\Events\UpdateEventRecapRequest;
use App\Models\Event;
use App\Models\EventPhoto;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

uses(TestCase::class);

function requestWithRecapEventRoute(UpdateEventRecapRequest $request, Event $event): UpdateEventRecapRequest
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

it('authorizes a user with the event manage permission', function () {
    $admin = makeMember();
    $admin->assignRole('admin');

    $request = new UpdateEventRecapRequest();
    $request->setUserResolver(fn () => $admin);

    expect($request->authorize())->toBeTrue();
});

it('denies a regular member without the event manage permission', function () {
    $member  = makeMember();
    $request = new UpdateEventRecapRequest();
    $request->setUserResolver(fn () => $member);

    expect($request->authorize())->toBeFalse();
});

it('validates the recap fields', function () {
    $request = new UpdateEventRecapRequest();

    $validator = Validator::make([
        'recap_summary'     => str_repeat('a', 1001),
        'recap_video_url_1' => 'not-a-url',
    ], $request->rules(), $request->messages());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('recap_summary'))->toBeTrue();
    expect($validator->errors()->has('recap_video_url_1'))->toBeTrue();
});

it('passes validation with valid recap data', function () {
    $event   = Event::factory()->create();
    $request = new UpdateEventRecapRequest();
    requestWithRecapEventRoute($request, $event);

    $validator = Validator::make([
        'recap_summary'     => 'Un super événement.',
        'recap_video_url_1' => 'https://example.com/video',
        'photos'            => [UploadedFile::fake()->create('photo1.jpg', 100, 'image/jpeg')],
    ], $request->rules(), $request->messages());
    $request->withValidator($validator);

    expect($validator->fails())->toBeFalse();
});

it('rejects new photos when the total exceeds 10', function () {
    $event = Event::factory()->create();

    for ($i = 0; $i < 9; $i++) {
        EventPhoto::create([
            'event_id' => $event->id,
            'path'     => "photos/existing-{$i}.jpg",
            'order'    => $i,
        ]);
    }

    $request = new UpdateEventRecapRequest();
    requestWithRecapEventRoute($request, $event);

    $photos = [
        UploadedFile::fake()->create('new1.jpg', 100, 'image/jpeg'),
        UploadedFile::fake()->create('new2.jpg', 100, 'image/jpeg'),
    ];
    $request->files->set('photos', $photos);

    $validator = Validator::make([
        'photos' => $photos,
    ], $request->rules(), $request->messages());
    $request->withValidator($validator);

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('photos'))->toBeTrue();
});
