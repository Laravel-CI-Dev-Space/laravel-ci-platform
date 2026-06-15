<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Forum;

use App\Http\Requests\Forum\StoreCommentRequest;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

uses(TestCase::class);

it('authorizes an active member with the comment permission', function () {
    $user    = makeMember();
    $request = new StoreCommentRequest();
    $request->setUserResolver(fn () => $user);

    expect($request->authorize())->toBeTrue();
});

it('denies a guest from creating a comment', function () {
    $request = new StoreCommentRequest();
    $request->setUserResolver(fn () => null);

    expect($request->authorize())->toBeFalse();
});

it('denies an inactive member from creating a comment', function () {
    $user = makeMember(['is_active' => false]);

    $request = new StoreCommentRequest();
    $request->setUserResolver(fn () => $user);

    expect($request->authorize())->toBeFalse();
});

it('validates the comment body length', function () {
    $request = new StoreCommentRequest();

    $validator = Validator::make(['body' => 'Hey'], $request->rules(), $request->messages());
    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('body'))->toBeTrue();

    $validator = Validator::make(['body' => 'Ceci est un commentaire valide.'], $request->rules(), $request->messages());
    expect($validator->fails())->toBeFalse();
});

it('validates that parent_id must exist', function () {
    $request = new StoreCommentRequest();

    $validator = Validator::make(['body' => 'Un commentaire valide', 'parent_id' => 999], $request->rules(), $request->messages());
    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('parent_id'))->toBeTrue();
});
