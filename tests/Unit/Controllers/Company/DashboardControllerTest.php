<?php

declare(strict_types=1);

namespace Tests\Unit\Controllers\Company;

use App\Http\Controllers\Company\DashboardController;
use App\Models\Company;
use App\Models\JobApplication;
use App\Models\JobOffer;
use Tests\TestCase;

uses(TestCase::class);

it('builds the dashboard overview with stats for the company account', function () {
    $company = Company::factory()->create();
    $account = makeCompanyAccount(['company_id' => $company->id]);

    $activeOffer = JobOffer::factory()->for($company)->active()->create();
    $draftOffer  = JobOffer::factory()->for($company)->create();

    JobApplication::create([
        'job_offer_id' => $activeOffer->id,
        'user_id'      => \App\Models\User::factory()->create()->id,
        'status'       => 'pending',
    ]);

    $this->actingAs($account, 'company');

    $view = (new DashboardController())->index();

    expect($view->name())->toBe('company.dashboard.overview');

    $data = $view->getData();

    expect($data['stats']['active_offers'])->toBe(1);
    expect($data['stats']['total_offers'])->toBe(2);
    expect($data['stats']['total_applications'])->toBe(1);
    expect($data['stats']['pending_applications'])->toBe(1);
    expect($data['recentApplications'])->toHaveCount(1);
    expect($data['activeOffers'])->toHaveCount(1);
});

it('returns empty stats when the account has no company', function () {
    $account = makeCompanyAccount();

    $this->actingAs($account, 'company');

    $view = (new DashboardController())->index();
    $data = $view->getData();

    expect($data['stats']['active_offers'])->toBe(0);
    expect($data['stats']['total_offers'])->toBe(0);
    expect($data['stats']['total_applications'])->toBe(0);
    expect($data['stats']['pending_applications'])->toBe(0);
    expect($data['recentApplications'])->toHaveCount(0);
    expect($data['activeOffers'])->toHaveCount(0);
});
