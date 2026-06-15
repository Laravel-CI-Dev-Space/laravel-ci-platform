<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Jobs;

use App\Http\Requests\Jobs\StoreJobOfferRequest;
use App\Models\JobOfferCategory;
use App\Models\JobSkill;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

uses(TestCase::class);

it('authorizes a member with the job offer create permission', function () {
    $user    = makeMember();
    $request = new StoreJobOfferRequest();
    $request->setUserResolver(fn () => $user);

    expect($request->authorize())->toBeTrue();
});

it('denies a guest without a company session', function () {
    $request = new StoreJobOfferRequest();
    $request->setUserResolver(fn () => null);

    expect($request->authorize())->toBeFalse();
});

it('validates required fields and the salary range', function () {
    $request = new StoreJobOfferRequest();

    $validator = Validator::make([
        'title'         => 'Dev',
        'description'   => 'Trop court',
        'contract_type' => 'not-a-type',
        'level'         => 'not-a-level',
        'salary_min'    => 5000,
        'salary_max'    => 1000,
        'categories'    => [],
        'skills'        => [],
    ], $request->rules(), $request->messages());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('title'))->toBeTrue();
    expect($validator->errors()->has('description'))->toBeTrue();
    expect($validator->errors()->has('contract_type'))->toBeTrue();
    expect($validator->errors()->has('level'))->toBeTrue();
    expect($validator->errors()->has('salary_max'))->toBeTrue();
    expect($validator->errors()->has('categories'))->toBeTrue();
    expect($validator->errors()->has('skills'))->toBeTrue();
});

it('passes validation with valid data', function () {
    $category = JobOfferCategory::create(['name' => 'Développement Web', 'slug' => 'developpement-web-test']);
    $skill    = JobSkill::create(['name' => 'Laravel Test', 'slug' => 'laravel-test']);

    $request = new StoreJobOfferRequest();

    $validator = Validator::make([
        'title'         => 'Développeur Laravel Senior',
        'description'   => str_repeat('Nous recherchons un développeur expérimenté. ', 5),
        'contract_type' => 'cdi',
        'level'         => 'senior',
        'salary_min'    => 30000,
        'salary_max'    => 50000,
        'categories'    => [$category->id],
        'skills'        => [$skill->id],
    ], $request->rules(), $request->messages());

    expect($validator->fails())->toBeFalse();
});
