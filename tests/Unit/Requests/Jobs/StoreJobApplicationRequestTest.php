<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Jobs;

use App\Http\Requests\Jobs\StoreJobApplicationRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

uses(TestCase::class);

it('authorizes an active member with the job apply permission', function () {
    $user    = makeMember();
    $request = new StoreJobApplicationRequest();
    $request->setUserResolver(fn () => $user);

    expect($request->authorize())->toBeTrue();
});

it('denies a guest from applying', function () {
    $request = new StoreJobApplicationRequest();
    $request->setUserResolver(fn () => null);

    expect($request->authorize())->toBeFalse();
});

it('denies an inactive member from applying', function () {
    $user    = makeMember(['is_active' => false]);
    $request = new StoreJobApplicationRequest();
    $request->setUserResolver(fn () => $user);

    expect($request->authorize())->toBeFalse();
});

it('validates the cover letter length and url fields', function () {
    $request = new StoreJobApplicationRequest();

    $validator = Validator::make([
        'cover_letter'  => 'Trop court',
        'portfolio_url' => 'not-a-url',
        'linkedin_url'  => 'not-a-url',
    ], $request->rules(), $request->messages());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('cover_letter'))->toBeTrue();
    expect($validator->errors()->has('portfolio_url'))->toBeTrue();
    expect($validator->errors()->has('linkedin_url'))->toBeTrue();
});

it('requires at least a cv or a cover letter', function () {
    $request   = new StoreJobApplicationRequest();
    $validator = Validator::make([], $request->rules(), $request->messages());
    $request->withValidator($validator);

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('cv'))->toBeTrue();
});

it('passes validation with a valid cover letter only', function () {
    $request = new StoreJobApplicationRequest();
    $data    = ['cover_letter' => str_repeat('Je suis très motivé. ', 5)];
    $request->merge($data);

    $validator = Validator::make($data, $request->rules(), $request->messages());
    $request->withValidator($validator);

    expect($validator->fails())->toBeFalse();
});

it('passes validation with a cv only', function () {
    $request = new StoreJobApplicationRequest();
    $cv      = UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf');
    $request->files->set('cv', $cv);

    $validator = Validator::make(['cv' => $cv], $request->rules(), $request->messages());
    $request->withValidator($validator);

    expect($validator->fails())->toBeFalse();
});
