<?php

declare(strict_types=1);

use App\Enums\Events\EventMediaType;
use App\Support\EventMediaFormData;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

uses(TestCase::class);

test('event media form data maps image upload path to url column', function () {
    $payload = EventMediaFormData::prepareForSave([
        'type'       => EventMediaType::IMAGE->value,
        'image_path' => 'events/media/photo.jpg',
    ]);

    expect($payload['url'])->toBe('events/media/photo.jpg');
    expect($payload)->not->toHaveKey('image_path');
});

test('event media form data maps video url to url column', function () {
    $payload = EventMediaFormData::prepareForSave([
        'type'      => EventMediaType::VIDEO->value,
        'video_url' => 'https://www.youtube.com/watch?v=abc12345678',
    ]);

    expect($payload['url'])->toBe('https://www.youtube.com/watch?v=abc12345678');
});

test('event media form data rejects empty url', function () {
    EventMediaFormData::prepareForSave([
        'type'       => EventMediaType::IMAGE->value,
        'image_path' => null,
    ]);
})->throws(ValidationException::class);

test('event media form data hydrates fields for editing', function () {
    $data = EventMediaFormData::hydrate([
        'type' => EventMediaType::PDF->value,
        'url'  => 'events/media/slides.pdf',
    ]);

    expect($data['document_path'])->toBe('events/media/slides.pdf');
});
