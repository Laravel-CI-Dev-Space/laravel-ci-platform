<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Blog;

use App\Http\Requests\Blog\StoreResourceRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

uses(TestCase::class);

it('authorizes an active member with the resource upload permission', function () {
    $user    = makeMember();
    $request = new StoreResourceRequest();
    $request->setUserResolver(fn () => $user);

    expect($request->authorize())->toBeTrue();
});

it('denies a guest from uploading a resource', function () {
    $request = new StoreResourceRequest();
    $request->setUserResolver(fn () => null);

    expect($request->authorize())->toBeFalse();
});

it('requires a title, a valid type and a file', function () {
    $request = new StoreResourceRequest();

    $validator = Validator::make([
        'title' => 'Abcd',
        'type'  => 'not-a-type',
    ], $request->rules(), $request->messages());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('title'))->toBeTrue();
    expect($validator->errors()->has('type'))->toBeTrue();
    expect($validator->errors()->has('file'))->toBeTrue();
});

it('passes validation with valid data', function () {
    $request = new StoreResourceRequest();

    $validator = Validator::make([
        'title' => 'Cheatsheet Laravel Eloquent',
        'type'  => 'cheatsheet',
        'file'  => UploadedFile::fake()->create('cheatsheet.pdf', 100, 'application/pdf'),
    ], $request->rules(), $request->messages());

    expect($validator->fails())->toBeFalse();
});
