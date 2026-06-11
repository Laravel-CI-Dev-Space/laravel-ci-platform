<?php

declare(strict_types=1);

use App\Mail\JobApplicationReceivedMail;
use App\Models\Company;
use App\Models\JobOffer;
use App\Models\User;
use App\Services\Jobs\CompanyService;
use App\Services\Jobs\JobOfferService;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    $this->seed(RoleSeeder::class);
});

test('admin can activate and deactivate a company', function () {
    $service = app(CompanyService::class);
    $company = Company::factory()->create(['is_active' => true]);

    $service->deactivate($company);
    expect($company->fresh()->isActive())->toBeFalse();

    $service->activate($company->fresh());
    expect($company->fresh()->isActive())->toBeTrue();
});

test('no email is sent when company is inactive', function () {
    Mail::fake();

    $company = Company::factory()->inactive()->withRecruitmentEmail('rh@inactive.ci')->create();
    $offer   = JobOffer::factory()->active()->create(['company_id' => $company->id]);
    $user    = User::factory()->membreActif()->create();

    app(JobOfferService::class)->apply($offer, $user);

    Mail::assertNothingSent();
});

test('email is sent when company is active with recruitment email', function () {
    Mail::fake();

    $company = Company::factory()->withRecruitmentEmail('rh@active.ci')->create();
    $offer   = JobOffer::factory()->active()->create(['company_id' => $company->id]);
    $user    = User::factory()->membreActif()->create();

    app(JobOfferService::class)->apply($offer, $user);

    Mail::assertSent(JobApplicationReceivedMail::class, fn (JobApplicationReceivedMail $mail) => $mail->hasTo('rh@active.ci'));
});
