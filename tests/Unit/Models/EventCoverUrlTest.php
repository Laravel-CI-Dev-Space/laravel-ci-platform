<?php

declare(strict_types=1);

use App\Models\Event;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

uses(TestCase::class);

test('coverUrl returns external url for seeded events', function () {
    $event = new Event([
        'cover' => 'https://picsum.photos/seed/lci-meetup-test/1200/525',
    ]);

    expect($event->coverUrl())->toBe('https://picsum.photos/seed/lci-meetup-test/1200/525');
});

test('coverUrl returns storage url for filament public disk uploads', function () {
    Storage::fake('public');

    $event = new Event(['cover' => 'events/laravel-ci-meetup.jpg']);

    expect($event->coverUrl())->toBe(Storage::disk('public')->url('events/laravel-ci-meetup.jpg'));
});

test('coverUrl returns asset url for static public assets', function () {
    $event = new Event(['cover' => 'assets/web/img/event-placeholder.jpg']);

    expect($event->coverUrl())->toBe(asset('assets/web/img/event-placeholder.jpg'));
});

test('coverUrl returns null when cover is empty', function () {
    $event = new Event(['cover' => null]);

    expect($event->coverUrl())->toBeNull();
});
