<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Blog;

use App\Enums\ResourceType;
use App\Models\Resource;
use App\Models\User;
use App\Services\AssetService;
use App\Services\Blog\ResourceService;
use Illuminate\Http\UploadedFile;
use Mockery;
use Tests\TestCase;

uses(TestCase::class);

it('lists public resources with an optional type filter', function () {
    $user = User::factory()->create();

    Resource::create([
        'user_id'   => $user->id,
        'title'     => 'Cheatsheet Laravel',
        'type'      => ResourceType::Cheatsheet,
        'file_path' => 'cheatsheet.pdf',
        'file_name' => 'cheatsheet.pdf',
        'file_size' => 1024,
        'mime_type' => 'application/pdf',
        'is_public' => true,
    ]);

    Resource::create([
        'user_id'   => $user->id,
        'title'     => 'Boilerplate privé',
        'type'      => ResourceType::Boilerplate,
        'file_path' => 'boilerplate.zip',
        'file_name' => 'boilerplate.zip',
        'file_size' => 2048,
        'mime_type' => 'application/zip',
        'is_public' => false,
    ]);

    Resource::create([
        'user_id'   => $user->id,
        'title'     => 'Boilerplate public',
        'type'      => ResourceType::Boilerplate,
        'file_path' => 'boilerplate-public.zip',
        'file_name' => 'boilerplate-public.zip',
        'file_size' => 2048,
        'mime_type' => 'application/zip',
        'is_public' => true,
    ]);

    $service = app(ResourceService::class);

    $all = $service->getResources();
    expect($all->total())->toBe(2);

    $boilerplates = $service->getResources('boilerplate');
    expect($boilerplates->total())->toBe(1);
    expect($boilerplates->first()->title)->toBe('Boilerplate public');
});

it('uploads a resource via the asset service', function () {
    $user = User::factory()->create();
    $file = UploadedFile::fake()->create('guide.pdf', 100, 'application/pdf');

    $assetService = Mockery::mock(AssetService::class);
    $assetService->shouldReceive('upload')
        ->once()
        ->with($file, 'resources', 'resource', $user->id)
        ->andReturn('resource_1_123456_abcdef.pdf');

    $service = new ResourceService($assetService);

    $resource = $service->uploadResource($user, [
        'title'       => 'Guide Laravel',
        'description' => 'Un guide complet',
        'type'        => 'guide',
        'is_public'   => true,
    ], $file);

    expect($resource->user_id)->toBe($user->id);
    expect($resource->title)->toBe('Guide Laravel');
    expect($resource->type)->toBe(ResourceType::Guide);
    expect($resource->file_path)->toBe('resource_1_123456_abcdef.pdf');
    expect($resource->file_name)->toBe('guide.pdf');
    expect($resource->downloads_count)->toBe(0);
});

it('increments the download counter when downloading a resource', function () {
    $user = User::factory()->create();

    $resource = Resource::create([
        'user_id'   => $user->id,
        'title'     => 'Cheatsheet Laravel',
        'type'      => ResourceType::Cheatsheet,
        'file_path' => 'cheatsheet.pdf',
        'file_name' => 'cheatsheet.pdf',
        'file_size' => 1024,
        'mime_type' => 'application/pdf',
        'is_public' => true,
        'downloads_count' => 0,
    ]);

    $service = app(ResourceService::class);
    $path    = $service->download($resource);

    expect($resource->refresh()->downloads_count)->toBe(1);
    expect($path)->toContain('cheatsheet.pdf');
});

it('deletes a resource and its file', function () {
    $user = User::factory()->create();

    $resource = Resource::create([
        'user_id'   => $user->id,
        'title'     => 'Cheatsheet Laravel',
        'type'      => ResourceType::Cheatsheet,
        'file_path' => 'cheatsheet.pdf',
        'file_name' => 'cheatsheet.pdf',
        'file_size' => 1024,
        'mime_type' => 'application/pdf',
        'is_public' => true,
    ]);

    $assetService = Mockery::mock(AssetService::class);
    $assetService->shouldReceive('delete')
        ->once()
        ->with('resources', 'cheatsheet.pdf');

    $service = new ResourceService($assetService);
    $service->deleteResource($resource);

    expect(Resource::find($resource->id))->toBeNull();
});
