<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Company;

use App\Http\Requests\Company\StoreCompanyRegistrationRequest;
use App\Models\CompanyRegistrationRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

uses(TestCase::class);

it('always authorizes the request', function () {
    $request = new StoreCompanyRegistrationRequest();

    expect($request->authorize())->toBeTrue();
});

it('requires the mandatory fields', function () {
    $request   = new StoreCompanyRegistrationRequest();
    $validator = Validator::make([], $request->rules(), $request->messages());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('first_name'))->toBeTrue();
    expect($validator->errors()->has('last_name'))->toBeTrue();
    expect($validator->errors()->has('company_name'))->toBeTrue();
    expect($validator->errors()->has('email'))->toBeTrue();
    expect($validator->errors()->has('position'))->toBeTrue();
    expect($validator->errors()->has('country'))->toBeTrue();
    expect($validator->errors()->has('business_domain'))->toBeTrue();
});

it('validates the email format and website url', function () {
    $request   = new StoreCompanyRegistrationRequest();
    $validator = Validator::make([
        'first_name'      => 'Jean',
        'last_name'       => 'Dupont',
        'company_name'    => 'Acme Corp',
        'email'           => 'not-an-email',
        'position'        => 'CEO',
        'country'         => 'Côte d\'Ivoire',
        'business_domain' => 'Technologie',
        'website'         => 'not-a-url',
    ], $request->rules(), $request->messages());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('email'))->toBeTrue();
    expect($validator->errors()->has('website'))->toBeTrue();
});

it('rejects an email that already requested registration', function () {
    CompanyRegistrationRequest::create([
        'first_name'      => 'Existing',
        'last_name'       => 'User',
        'company_name'    => 'Existing Corp',
        'email'           => 'existing@example.com',
        'position'        => 'CEO',
        'country'         => 'Côte d\'Ivoire',
        'business_domain' => 'Technologie',
        'status'          => 'pending',
    ]);

    $request   = new StoreCompanyRegistrationRequest();
    $validator = Validator::make([
        'first_name'      => 'Jean',
        'last_name'       => 'Dupont',
        'company_name'    => 'Acme Corp',
        'email'           => 'existing@example.com',
        'position'        => 'CEO',
        'country'         => 'Côte d\'Ivoire',
        'business_domain' => 'Technologie',
    ], $request->rules(), $request->messages());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('email'))->toBeTrue();
});

it('passes validation with valid data', function () {
    $request   = new StoreCompanyRegistrationRequest();
    $validator = Validator::make([
        'first_name'      => 'Jean',
        'last_name'       => 'Dupont',
        'company_name'    => 'Acme Corp',
        'email'           => 'jean.dupont@example.com',
        'position'        => 'CEO',
        'country'         => 'Côte d\'Ivoire',
        'business_domain' => 'Technologie',
        'motivation'      => 'Nous souhaitons recruter des développeurs.',
    ], $request->rules(), $request->messages());

    expect($validator->fails())->toBeFalse();
});
