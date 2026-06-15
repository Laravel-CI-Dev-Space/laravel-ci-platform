<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Forum;

use App\Http\Requests\Forum\StoreReportRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

uses(TestCase::class);

it('authorizes an active member with the report permission', function () {
    $user    = makeMember();
    $request = new StoreReportRequest();
    $request->setUserResolver(fn () => $user);

    expect($request->authorize())->toBeTrue();
});

it('denies a guest from reporting content', function () {
    $request = new StoreReportRequest();
    $request->setUserResolver(fn () => null);

    expect($request->authorize())->toBeFalse();
});

it('validates the reason against the allowed list', function () {
    $request = new StoreReportRequest();

    $validator = Validator::make(['reason' => 'spam'], $request->rules(), $request->messages());
    expect($validator->fails())->toBeFalse();

    $validator = Validator::make(['reason' => 'not-a-valid-reason'], $request->rules(), $request->messages());
    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('reason'))->toBeTrue();
});

it('requires a reason and limits the description length', function () {
    $request = new StoreReportRequest();

    $validator = Validator::make([], $request->rules(), $request->messages());
    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('reason'))->toBeTrue();

    $validator = Validator::make([
        'reason'      => 'other',
        'description' => str_repeat('a', 501),
    ], $request->rules(), $request->messages());
    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('description'))->toBeTrue();
});
