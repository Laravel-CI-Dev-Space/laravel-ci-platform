<?php

declare(strict_types=1);

use App\Enums\Events\EventMediaType;
use App\Models\Event;
use App\Models\EventMedia;
use Illuminate\Support\Facades\Storage;

test('event media resolves external and storage urls', function () {
    Storage::fake('public');

    $external = new EventMedia(['url' => 'https://www.youtube.com/watch?v=abc12345678', 'type' => EventMediaType::VIDEO]);
    $stored   = new EventMedia(['url' => 'events/media/slide.pdf', 'type' => EventMediaType::PDF]);

    expect($external->resolvedUrl())->toBe('https://www.youtube.com/watch?v=abc12345678');
    expect($stored->resolvedUrl())->toBe(Storage::disk('public')->url('events/media/slide.pdf'));
});

test('youtube embed url is extracted from video media', function () {
    $media = new EventMedia([
        'type' => EventMediaType::VIDEO,
        'url'  => 'https://youtu.be/dQw4w9WgXcQ',
    ]);

    expect($media->youtubeEmbedUrl())->toBe('https://www.youtube.com/embed/dQw4w9WgXcQ');
});

test('past event detail page shows media section', function () {
    $event = Event::factory()->published()->past()->create(['title' => 'Meetup archives']);

    EventMedia::factory()->for($event)->video()->create([
        'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
    ]);

    EventMedia::factory()->for($event)->pdf()->create([
        'url' => 'events/media/support.pdf',
    ]);

    $this->get(route('events.show', $event))
        ->assertOk()
        ->assertSee('Replay')
        ->assertSee('ressources')
        ->assertSee('youtube.com/embed/dQw4w9WgXcQ')
        ->assertSee('Télécharger');
});

test('upcoming event detail page shows media section when resources exist', function () {
    $event = Event::factory()->published()->upcoming()->create(['title' => 'Meetup à venir']);

    EventMedia::factory()->for($event)->video()->create();

    $this->get(route('events.show', $event))
        ->assertOk()
        ->assertSee('Ressources')
        ->assertSee('youtube.com/embed/dQw4w9WgXcQ');
});

test('event media resolves json encoded upload paths', function () {
    Storage::fake('public');

    $media = new EventMedia([
        'url'  => '["events/media/photo.jpg"]',
        'type' => EventMediaType::IMAGE,
    ]);

    expect($media->resolvedUrl())->toBe(Storage::disk('public')->url('events/media/photo.jpg'));
});
