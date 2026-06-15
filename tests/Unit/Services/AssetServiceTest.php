<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\AssetService;
use Illuminate\Http\UploadedFile;
use Mockery;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

uses(TestCase::class);

it('uploads a public file and returns a generated filename', function () {
    File::shouldReceive('isDirectory')->once()->andReturn(true);
    File::shouldReceive('put')->once()->with(
        Mockery::on(fn ($path) => str_contains($path, 'assets' . DIRECTORY_SEPARATOR . 'avatars')),
        'avatar content'
    );

    $file = UploadedFile::fake()->create('avatar.jpg', 10, 'image/jpeg');
    file_put_contents($file->getRealPath(), 'avatar content');

    $service  = new AssetService();
    $filename = $service->upload($file, 'avatars', 'avatar', 42);

    expect($filename)->toStartWith('avatar_42_');
    expect($filename)->toEndWith('.jpg');
});

it('creates the destination directory when it does not exist', function () {
    File::shouldReceive('isDirectory')->once()->andReturn(false);
    File::shouldReceive('makeDirectory')->once()->with(Mockery::type('string'), 0755, true);
    File::shouldReceive('put')->once();

    $file = UploadedFile::fake()->create('logo.png', 10, 'image/png');

    $service  = new AssetService();
    $filename = $service->upload($file, 'company-logos', 'logo', 1);

    expect($filename)->toStartWith('logo_1_');
    expect($filename)->toEndWith('.png');
});

it('deletes the previous file before uploading a new one', function () {
    File::shouldReceive('isDirectory')->once()->andReturn(true);
    File::shouldReceive('exists')->once()->andReturn(true);
    File::shouldReceive('delete')->once()->with(Mockery::on(
        fn ($path) => str_contains($path, 'old-avatar.jpg')
    ));
    File::shouldReceive('put')->once();

    $file = UploadedFile::fake()->create('new-avatar.jpg', 10, 'image/jpeg');

    $service  = new AssetService();
    $service->upload($file, 'avatars', 'avatar', 42, 'old-avatar.jpg');
});

it('deletes a file when it exists', function () {
    File::shouldReceive('exists')->once()->andReturn(true);
    File::shouldReceive('delete')->once();

    (new AssetService())->delete('avatars', 'avatar.jpg');
});

it('does not attempt to delete a file that does not exist', function () {
    File::shouldReceive('exists')->once()->andReturn(false);
    File::shouldReceive('delete')->never();

    (new AssetService())->delete('avatars', 'missing.jpg');
});

it('stores a private file on the local disk', function () {
    Storage::fake('local');

    $file = UploadedFile::fake()->create('cv.pdf', 10, 'application/pdf');

    $filename = (new AssetService())->uploadPrivate($file, 'cv', 'cv', 7);

    expect($filename)->toStartWith('cv_7_');
    expect($filename)->toEndWith('.pdf');
    Storage::disk('local')->assertExists("cv/{$filename}");
});

it('deletes the previous private file before uploading a new one', function () {
    Storage::fake('local');
    Storage::disk('local')->put('cv/old-cv.pdf', 'old content');

    $file = UploadedFile::fake()->create('new-cv.pdf', 10, 'application/pdf');

    (new AssetService())->uploadPrivate($file, 'cv', 'cv', 7, 'old-cv.pdf');

    Storage::disk('local')->assertMissing('cv/old-cv.pdf');
});

it('deletes a private file from the local disk', function () {
    Storage::fake('local');
    Storage::disk('local')->put('cv/sample.pdf', 'content');

    (new AssetService())->deletePrivate('cv', 'sample.pdf');

    Storage::disk('local')->assertMissing('cv/sample.pdf');
});

it('builds the public url for a stored file', function () {
    $url = (new AssetService())->url('avatars', 'avatar_1_123.jpg');

    expect($url)->toContain('assets/avatars/avatar_1_123.jpg');
});
