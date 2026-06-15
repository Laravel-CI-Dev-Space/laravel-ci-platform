<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Events;

use App\Enums\EventRegistrationStatus;
use App\Models\Event;
use App\Models\EventPhoto;
use App\Models\EventRegistration;
use App\Models\User;
use App\Services\AssetService;
use App\Services\Events\EventRecapService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Mockery;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    Mail::fake();
});

it('updates the recap summary, content and video links', function () {
    $event = Event::factory()->past()->create();

    $updated = app(EventRecapService::class)->updateRecap($event, [
        'recap_summary' => 'Un super événement.',
        'recap_content' => 'Détails du récapitulatif.',
        'recap_video_url_1' => 'https://youtube.com/watch?v=abc',
    ]);

    expect($updated->recap_summary)->toBe('Un super événement.');
    expect($updated->recap_content)->toBe('Détails du récapitulatif.');
    expect($updated->recap_video_url_1)->toBe('https://youtube.com/watch?v=abc');
});

it('uploads and deletes a recap document via the asset service', function () {
    $event = Event::factory()->past()->create();
    $user  = User::factory()->create();
    $file  = UploadedFile::fake()->create('recap.pdf', 100, 'application/pdf');

    $assetService = Mockery::mock(AssetService::class);
    $assetService->shouldReceive('upload')
        ->once()
        ->with($file, 'documents/events', 'event-recap', $user->id, null)
        ->andReturn('event-recap_1_123_abcdef.pdf');
    $assetService->shouldReceive('delete')
        ->once()
        ->with('documents/events', 'event-recap_1_123_abcdef.pdf');

    $service = new EventRecapService($assetService, app(\App\Services\NotificationService::class));

    $updated = $service->uploadDocument($event, $file, $user->id);
    expect($updated->recap_document_path)->toBe('event-recap_1_123_abcdef.pdf');
    expect($updated->recap_document_name)->toBe('recap.pdf');

    $deleted = $service->deleteDocument($updated);
    expect($deleted->recap_document_path)->toBeNull();
    expect($deleted->recap_document_name)->toBeNull();
});

it('adds photos to the recap up to the maximum and deletes them', function () {
    $event = Event::factory()->past()->create();
    $user  = User::factory()->create();
    $files = [
        UploadedFile::fake()->create('photo1.jpg', 100, 'image/jpeg'),
        UploadedFile::fake()->create('photo2.jpg', 100, 'image/jpeg'),
    ];

    $assetService = Mockery::mock(AssetService::class);
    $assetService->shouldReceive('upload')
        ->twice()
        ->andReturn('photo1.jpg', 'photo2.jpg');
    $assetService->shouldReceive('delete')->once()->with('events/recap', 'photo1.jpg');

    $service = new EventRecapService($assetService, app(\App\Services\NotificationService::class));
    $service->addPhotos($event, $files, $user->id);

    expect($event->photos()->count())->toBe(2);

    $first = $event->photos()->orderBy('order')->first();
    $service->deletePhoto($first);

    expect($event->photos()->count())->toBe(1);
});

it('throws when adding photos would exceed the maximum of 10', function () {
    $event = Event::factory()->past()->create();
    $user  = User::factory()->create();

    for ($i = 0; $i < 10; $i++) {
        EventPhoto::create(['event_id' => $event->id, 'path' => "photo{$i}.jpg", 'order' => $i]);
    }

    $assetService = Mockery::mock(AssetService::class);
    $service = new EventRecapService($assetService, app(\App\Services\NotificationService::class));

    expect(fn () => $service->addPhotos($event, [UploadedFile::fake()->create('extra.jpg', 100, 'image/jpeg')], $user->id))
        ->toThrow(\Exception::class);
});

it('reorders recap photos', function () {
    $event = Event::factory()->past()->create();

    $first  = EventPhoto::create(['event_id' => $event->id, 'path' => 'a.jpg', 'order' => 0]);
    $second = EventPhoto::create(['event_id' => $event->id, 'path' => 'b.jpg', 'order' => 1]);

    app(EventRecapService::class)->reorderPhotos($event, [$second->id, $first->id]);

    expect($second->refresh()->order)->toBe(0);
    expect($first->refresh()->order)->toBe(1);
});

it('publishes a recap for a completed event and notifies confirmed registrants', function () {
    $admin = User::factory()->create();
    $event = Event::factory()->past()->create();

    $registrant = User::factory()->create();
    EventRegistration::create([
        'event_id'      => $event->id,
        'user_id'       => $registrant->id,
        'status'        => EventRegistrationStatus::Confirmed,
        'ical_token'    => 'token-recap',
        'registered_at' => now(),
    ]);

    $updated = app(EventRecapService::class)->publish($admin, $event);

    expect($updated->recap_published_at)->not->toBeNull();
    expect($updated->recap_published_by)->toBe($admin->id);

    Mail::assertQueued(\App\Mail\EventRecapPublishedMail::class);
});

it('refuses to publish a recap for a non-completed event', function () {
    $admin = User::factory()->create();
    $event = Event::factory()->published()->create();

    expect(fn () => app(EventRecapService::class)->publish($admin, $event))
        ->toThrow(\Exception::class);
});

it('unpublishes a recap', function () {
    $event = Event::factory()->past()->create([
        'recap_published_at' => now(),
        'recap_published_by' => User::factory()->create()->id,
    ]);

    $updated = app(EventRecapService::class)->unpublish($event);

    expect($updated->recap_published_at)->toBeNull();
    expect($updated->recap_published_by)->toBeNull();
});
