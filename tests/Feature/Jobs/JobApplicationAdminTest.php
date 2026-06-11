<?php

declare(strict_types=1);

use App\Enums\Jobs\JobApplicationStatus;
use App\Enums\Jobs\JobOfferStatus;
use App\Mail\JobApplicationReceivedMail;
use App\Models\Company;
use App\Models\JobApplication;
use App\Models\JobOffer;
use App\Models\User;
use App\Services\Jobs\JobApplicationService;
use App\Services\Jobs\JobOfferService;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    $this->seed(RoleSeeder::class);
});

test('company receives email when member applies and recruitment email is set', function () {
    Mail::fake();

    $company = Company::factory()->withRecruitmentEmail('rh@tech-ci.dev')->create();
    $offer   = JobOffer::factory()->active()->create(['company_id' => $company->id]);
    $user    = User::factory()->membreActif()->create();

    app(JobOfferService::class)->apply($offer, $user, 'Motivé.');

    Mail::assertSent(JobApplicationReceivedMail::class, function (JobApplicationReceivedMail $mail) {
        return $mail->hasTo('rh@tech-ci.dev');
    });
});

test('no email is sent when company recruitment email is not configured', function () {
    Mail::fake();

    $company = Company::factory()->create(['email' => null]);
    $offer   = JobOffer::factory()->active()->create(['company_id' => $company->id]);
    $user    = User::factory()->membreActif()->create();

    app(JobOfferService::class)->apply($offer, $user);

    Mail::assertNothingSent();
});

test('accepting application keeps offer active', function () {
    $offer = JobOffer::factory()->active()->create();
    $application = JobApplication::factory()->create([
        'job_offer_id' => $offer->id,
        'status'       => JobApplicationStatus::PENDING,
    ]);

    app(JobApplicationService::class)->accept($application);

    expect($application->fresh()->status)->toBe(JobApplicationStatus::ACCEPTED);
    expect($offer->fresh()->status)->toBe(JobOfferStatus::ACTIVE);
});

test('admin can publish and deactivate job offers', function () {
    $service = app(JobApplicationService::class);

    $draft = JobOffer::factory()->draft()->create();
    $service->publishOffer($draft);
    expect($draft->fresh()->status)->toBe(JobOfferStatus::ACTIVE);

    $service->deactivateOffer($draft->fresh());
    expect($draft->fresh()->status)->toBe(JobOfferStatus::EXPIRED);
});
