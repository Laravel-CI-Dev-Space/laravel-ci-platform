<?php

declare(strict_types=1);

namespace Tests\Unit\Controllers\Company;

use App\Http\Controllers\Company\ApplicationController;
use App\Models\Company;
use App\Models\JobApplication;
use App\Models\JobOffer;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

uses(TestCase::class);

it('lists applications for an offer owned by the company', function () {
    $company = Company::factory()->create();
    $account = makeCompanyAccount(['company_id' => $company->id]);
    $offer   = JobOffer::factory()->for($company)->create();

    JobApplication::create([
        'job_offer_id' => $offer->id,
        'user_id'      => User::factory()->create()->id,
        'status'       => 'pending',
    ]);

    $this->actingAs($account, 'company');

    $view = app(ApplicationController::class)->index($offer);

    expect($view->name())->toBe('company.dashboard.applications.index');
    expect($view->getData()['applications'])->toHaveCount(1);
});

it('denies listing applications for an offer that belongs to another company', function () {
    $company      = Company::factory()->create();
    $otherCompany = Company::factory()->create();
    $account      = makeCompanyAccount(['company_id' => $company->id]);
    $offer        = JobOffer::factory()->for($otherCompany)->create();

    $this->actingAs($account, 'company');

    try {
        app(ApplicationController::class)->index($offer);
        $this->fail('Expected an HttpException to be thrown.');
    } catch (HttpException $e) {
        expect($e->getStatusCode())->toBe(Response::HTTP_FORBIDDEN);
    }
});

it('denies viewing an application that belongs to another company', function () {
    $company      = Company::factory()->create();
    $otherCompany = Company::factory()->create();
    $account      = makeCompanyAccount(['company_id' => $company->id]);
    $offer        = JobOffer::factory()->for($otherCompany)->create();

    $application = JobApplication::create([
        'job_offer_id' => $offer->id,
        'user_id'      => User::factory()->create()->id,
        'status'       => 'pending',
    ]);

    $this->actingAs($account, 'company');

    try {
        app(ApplicationController::class)->show($application);
        $this->fail('Expected an HttpException to be thrown.');
    } catch (HttpException $e) {
        expect($e->getStatusCode())->toBe(Response::HTTP_FORBIDDEN);
    }
});

it('updates the application status and redirects back with a success message', function () {
    $company = Company::factory()->create();
    $account = makeCompanyAccount(['company_id' => $company->id]);
    $offer   = JobOffer::factory()->for($company)->create();

    $application = JobApplication::create([
        'job_offer_id' => $offer->id,
        'user_id'      => User::factory()->create()->id,
        'status'       => 'pending',
    ]);

    $response = $this->actingAs($account, 'company')
        ->from(route('company.applications.show', $application))
        ->patch(route('company.applications.status', $application), [
            'status' => 'shortlisted',
        ]);

    $response->assertRedirect(route('company.applications.show', $application));
    $response->assertSessionHas('success');

    expect($application->refresh()->status->value)->toBe('shortlisted');
});

it('denies updating the status of an application that belongs to another company', function () {
    $company      = Company::factory()->create();
    $otherCompany = Company::factory()->create();
    $account      = makeCompanyAccount(['company_id' => $company->id]);
    $offer        = JobOffer::factory()->for($otherCompany)->create();

    $application = JobApplication::create([
        'job_offer_id' => $offer->id,
        'user_id'      => User::factory()->create()->id,
        'status'       => 'pending',
    ]);

    $response = $this->actingAs($account, 'company')
        ->patch(route('company.applications.status', $application), [
            'status' => 'shortlisted',
        ]);

    $response->assertForbidden();
});

it('downloads the candidate cv when it exists', function () {
    Storage::fake('local');
    Storage::disk('local')->put('cv/sample-cv.pdf', 'PDF content');

    $company = Company::factory()->create();
    $account = makeCompanyAccount(['company_id' => $company->id]);
    $offer   = JobOffer::factory()->for($company)->create();

    $application = JobApplication::create([
        'job_offer_id' => $offer->id,
        'user_id'      => User::factory()->create()->id,
        'status'       => 'pending',
        'cv_path'      => 'sample-cv.pdf',
    ]);

    $response = $this->actingAs($account, 'company')
        ->get(route('company.applications.cv', $application));

    $response->assertOk();
});

it('returns 404 when the application has no cv', function () {
    $company = Company::factory()->create();
    $account = makeCompanyAccount(['company_id' => $company->id]);
    $offer   = JobOffer::factory()->for($company)->create();

    $application = JobApplication::create([
        'job_offer_id' => $offer->id,
        'user_id'      => User::factory()->create()->id,
        'status'       => 'pending',
    ]);

    $response = $this->actingAs($account, 'company')
        ->get(route('company.applications.cv', $application));

    $response->assertNotFound();
});
