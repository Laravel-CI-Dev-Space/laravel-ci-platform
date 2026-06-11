<?php

declare(strict_types=1);

use App\Actions\Jobs\MatchJobAlertsForOfferAction;
use App\Enums\Jobs\JobOfferType;
use App\Livewire\Dashboard\MemberJobAlerts;
use App\Mail\JobAlertMail;
use App\Models\JobAlert;
use App\Models\JobOffer;
use App\Models\Profile;
use App\Models\User;
use App\Services\Jobs\JobAlertService;
use App\Services\Jobs\JobApplicationService;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(RoleSeeder::class);
});

function memberWithProfile(): User
{
    $user = User::factory()->membreActif()->create();
    Profile::create(['user_id' => $user->id]);

    return $user;
}

test('match action finds alerts by keywords location and type', function () {
    $matcher = app(MatchJobAlertsForOfferAction::class);

    $user = User::factory()->membreActif()->create();
    $offer = JobOffer::factory()->active()->create([
        'title'       => 'Développeur Laravel senior',
        'description' => 'Stack PHP Symfony et APIs REST',
        'location'    => 'Abidjan, Côte d\'Ivoire',
        'type'        => JobOfferType::CDI,
    ]);

    $matching = JobAlert::factory()->for($user)->create([
        'keywords' => 'laravel',
        'location' => 'abidjan',
        'type'     => JobOfferType::CDI,
    ]);

    JobAlert::factory()->for($user)->create([
        'keywords' => 'python',
        'location' => null,
        'type'     => null,
    ]);

    $results = $matcher->execute($offer);

    expect($results)->toHaveCount(1);
    expect($results->first()->id)->toBe($matching->id);
});

test('keyword matching is accent insensitive', function () {
    $matcher = app(MatchJobAlertsForOfferAction::class);
    $user    = User::factory()->membreActif()->create();

    $offer = JobOffer::factory()->active()->create([
        'title'       => 'Développeur Laravel',
        'description' => 'API REST',
        'location'    => 'Abidjan',
    ]);

    JobAlert::factory()->for($user)->create(['keywords' => 'developpeur']);

    expect($matcher->execute($offer))->toHaveCount(1);
});

test('keyword matching supports comma separated terms and company name', function () {
    $matcher = app(MatchJobAlertsForOfferAction::class);
    $user    = User::factory()->membreActif()->create();

    $offer = JobOffer::factory()->active()->create([
        'title'       => 'Ingénieur backend',
        'description' => 'Maintenance applicative',
        'location'    => 'Remote',
    ]);

    JobAlert::factory()->for($user)->create(['keywords' => 'laravel, symfony']);

    expect($matcher->execute($offer))->toBeEmpty();

    $offer->update(['description' => 'Stack Laravel et Symfony']);

    expect($matcher->execute($offer->fresh()))->toHaveCount(1);
});

test('inactive alerts are excluded from matching', function () {
    $matcher = app(MatchJobAlertsForOfferAction::class);
    $user    = User::factory()->membreActif()->create();
    $offer   = JobOffer::factory()->active()->create(['title' => 'Laravel Developer']);

    JobAlert::factory()->for($user)->inactive()->create(['keywords' => 'laravel']);

    expect($matcher->execute($offer))->toBeEmpty();
});

test('publishing an offer sends job alert emails to matching users', function () {
    Mail::fake();

    $user  = User::factory()->membreActif()->create();
    $offer = JobOffer::factory()->draft()->create([
        'title'       => 'Ingénieur Laravel',
        'description' => 'Projet communautaire',
        'location'    => 'Remote',
        'type'        => JobOfferType::REMOTE,
    ]);

    JobAlert::factory()->for($user)->create([
        'keywords' => 'laravel',
        'location' => 'remote',
        'type'     => JobOfferType::REMOTE,
    ]);

    app(JobApplicationService::class)->publishOffer($offer);

    Mail::assertSent(JobAlertMail::class, fn (JobAlertMail $mail) => $mail->hasTo($user->email));
});

test('only one email is sent when user has multiple matching alerts', function () {
    Mail::fake();

    $user  = User::factory()->membreActif()->create();
    $offer = JobOffer::factory()->draft()->create([
        'title'       => 'Backend Laravel',
        'description' => 'API REST',
        'location'    => 'Abidjan',
    ]);

    JobAlert::factory()->for($user)->create(['keywords' => 'laravel']);
    JobAlert::factory()->for($user)->create(['keywords' => 'backend']);

    app(JobApplicationService::class)->publishOffer($offer);

    Mail::assertSent(JobAlertMail::class, 1);
});

test('member can create and manage alerts from dashboard', function () {
    $user = memberWithProfile();

    Livewire::actingAs($user)
        ->test(MemberJobAlerts::class)
        ->set('keywords', 'laravel')
        ->set('location', 'Abidjan')
        ->call('createAlert')
        ->assertHasNoErrors()
        ->assertDispatched('app-toast', message: 'Alerte créée', type: 'success')
        ->assertSee('laravel')
        ->assertSee('Abidjan');

    $this->assertDatabaseHas('job_alerts', [
        'user_id'  => $user->id,
        'keywords' => 'laravel',
        'location' => 'Abidjan',
    ]);

    $alert = JobAlert::query()->where('user_id', $user->id)->first();

    Livewire::actingAs($user)
        ->test(MemberJobAlerts::class)
        ->call('toggleActive', $alert->id)
        ->assertDispatched('app-toast', message: 'Alerte désactivée', type: 'info')
        ->call('openDeleteModal', $alert->id)
        ->call('confirmDelete')
        ->assertDispatched('app-toast', message: 'Alerte supprimée', type: 'info');

    $this->assertDatabaseMissing('job_alerts', ['id' => $alert->id]);
});

test('creating alert without criteria fails validation', function () {
    $user = memberWithProfile();

    Livewire::actingAs($user)
        ->test(MemberJobAlerts::class)
        ->call('createAlert')
        ->assertHasErrors(['criteria']);
});

test('job alert service rejects alert without criteria', function () {
    $user = User::factory()->membreActif()->create();

    app(JobAlertService::class)->create($user, [
        'keywords' => null,
        'location' => null,
        'type'     => null,
    ]);
})->throws(\Illuminate\Validation\ValidationException::class);
