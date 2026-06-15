<?php

declare(strict_types=1);

namespace Tests\Unit\Controllers\Company;

use App\Http\Controllers\Company\JobOfferController;
use App\Models\Company;
use App\Models\JobOffer;
use App\Models\JobOfferCategory;
use App\Models\JobSkill;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

uses(TestCase::class);

it('lists the job offers belonging to the authenticated company', function () {
    $company = Company::factory()->create();
    $account = makeCompanyAccount(['company_id' => $company->id]);

    JobOffer::factory()->for($company)->count(3)->create();

    $this->actingAs($account, 'company');

    $view = app(JobOfferController::class)->index();

    expect($view->name())->toBe('company.dashboard.offers.index');
    expect($view->getData()['offers'])->toHaveCount(3);
});

it('returns an empty collection when the account has no company', function () {
    $account = makeCompanyAccount();

    $this->actingAs($account, 'company');

    $view = app(JobOfferController::class)->index();

    expect($view->getData()['offers'])->toHaveCount(0);
});

it('shows the offer creation form with categories and skills', function () {
    JobOfferCategory::create(['name' => 'Développement Mobile', 'slug' => 'developpement-mobile-test']);
    JobSkill::create(['name' => 'Flutter', 'slug' => 'flutter-test']);

    $view = app(JobOfferController::class)->create();

    expect($view->name())->toBe('company.dashboard.offers.create');
    expect($view->getData()['categories']->pluck('slug'))->toContain('developpement-mobile-test');
    expect($view->getData()['skills']->pluck('slug'))->toContain('flutter-test');
});

it('denies a company from viewing another company\'s offer', function () {
    $company      = Company::factory()->create();
    $otherCompany = Company::factory()->create();
    $account      = makeCompanyAccount(['company_id' => $company->id]);
    $offer        = JobOffer::factory()->for($otherCompany)->create();

    $this->actingAs($account, 'company');

    expect(fn () => app(JobOfferController::class)->show($offer))
        ->toThrow(HttpException::class);

    try {
        app(JobOfferController::class)->show($offer);
    } catch (HttpException $e) {
        expect($e->getStatusCode())->toBe(Response::HTTP_FORBIDDEN);
    }
});

it('stores a new job offer and redirects to the offers index', function () {
    $company  = Company::factory()->create();
    $account  = makeCompanyAccount(['company_id' => $company->id]);
    $category = JobOfferCategory::create(['name' => 'Développement Backend', 'slug' => 'developpement-backend-test']);
    $skill    = JobSkill::create(['name' => 'PHP Test', 'slug' => 'php-test']);

    $response = $this->actingAs($account, 'company')->post(route('company.offers.store'), [
        'title'         => 'Développeur Laravel Senior',
        'description'   => str_repeat('Nous recherchons un développeur expérimenté. ', 5),
        'contract_type' => 'cdi',
        'level'         => 'senior',
        'currency'      => 'XOF',
        'categories'    => [$category->id],
        'skills'        => [$skill->id],
    ]);

    $response->assertRedirect(route('company.offers.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('job_offers', [
        'company_id' => $company->id,
        'title'      => 'Développeur Laravel Senior',
    ]);
});
