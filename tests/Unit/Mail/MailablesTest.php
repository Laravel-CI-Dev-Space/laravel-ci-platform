<?php

declare(strict_types=1);

namespace Tests\Unit\Mail;

use App\Enums\EventRegistrationStatus;
use App\Mail\ArticlePublishedMail;
use App\Mail\CompanyAccessMail;
use App\Mail\CompanyRegistrationRejectedMail;
use App\Mail\EventConfirmationMail;
use App\Mail\EventRecapPublishedMail;
use App\Mail\EventReminderMail;
use App\Mail\JobAlertMail;
use App\Mail\MentionMail;
use App\Mail\NewAnswerMail;
use App\Mail\NewJobApplicationMail;
use App\Mail\WelcomeMail;
use App\Models\Answer;
use App\Models\Article;
use App\Models\Company;
use App\Models\CompanyRegistrationRequest;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\JobApplication;
use App\Models\JobOffer;
use App\Models\Question;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    Mail::fake();
});

it('builds the welcome mail with the user\'s data', function () {
    $user = User::factory()->create(['name' => 'Awa Koné']);

    $mail = new WelcomeMail($user);

    $mail->assertHasSubject('🐘 Bienvenue dans la communauté Laravel CI !');
    $mail->assertSeeInHtml('Awa Koné');
});

it('builds the article published mail with the article title', function () {
    $user    = User::factory()->create();
    $article = Article::factory()->for($user, 'author')->create(['title' => 'Maîtriser Eloquent en 10 minutes']);

    $mail = new ArticlePublishedMail($user, $article);

    $mail->assertHasSubject('Votre article a été publié — Laravel CI');
    $mail->assertSeeInHtml('Maîtriser Eloquent en 10 minutes');
});

it('builds the company access mail with the temporary password', function () {
    $company = Company::factory()->create(['name' => 'TechCorp Abidjan']);
    $account = makeCompanyAccount(['company_id' => $company->id, 'first_name' => 'Yao']);

    $mail = new CompanyAccessMail($account, 'T3mp0raryPass!');

    $mail->assertHasSubject('Votre compte entreprise Laravel CI est activé');
    $mail->assertSeeInHtml('T3mp0raryPass!');
    $mail->assertSeeInHtml('TechCorp Abidjan');
    $mail->assertSeeInHtml($account->email);
});

it('builds the company registration rejected mail with the reason', function () {
    $request = CompanyRegistrationRequest::create([
        'first_name'      => 'Marc',
        'last_name'       => 'Diallo',
        'company_name'    => 'TechCorp',
        'email'           => 'marc@techcorp.test',
        'position'        => 'CEO',
        'business_domain' => 'Informatique',
        'status'          => 'rejected',
    ]);

    $mail = new CompanyRegistrationRejectedMail($request, 'Domaine d\'activité non éligible.');

    $mail->assertHasSubject("Votre demande d'inscription — Laravel CI");
    $mail->assertSeeInHtml('Domaine d\'activité non éligible.');
    $mail->assertSeeInHtml('TechCorp');
});

it('builds the event confirmation mail with the event title', function () {
    $user  = User::factory()->create();
    $event = Event::factory()->published()->create(['title' => 'Laravel Meetup Abidjan']);

    $registration = EventRegistration::create([
        'event_id'      => $event->id,
        'user_id'       => $user->id,
        'status'        => EventRegistrationStatus::Confirmed,
        'ical_token'    => 'token-mail-confirm',
        'registered_at' => now(),
    ]);

    $mail = new EventConfirmationMail($user, $registration);

    $mail->assertHasSubject("Inscription confirmée : Laravel Meetup Abidjan — Laravel CI");
    $mail->assertSeeInHtml('Laravel Meetup Abidjan');
});

it('builds the event recap published mail with the event title', function () {
    $user  = User::factory()->create();
    $event = Event::factory()->past()->create(['title' => 'Hackathon Laravel CI']);

    $mail = new EventRecapPublishedMail($user, $event);

    $mail->assertHasSubject("Récapitulatif disponible : Hackathon Laravel CI — Laravel CI");
    $mail->assertSeeInHtml('Hackathon Laravel CI');
});

it('builds the event reminder mail with a 7-day and 1-day subject', function () {
    $user  = User::factory()->create();
    $event = Event::factory()->published()->create(['title' => 'Conférence Laravel']);

    $registration = EventRegistration::create([
        'event_id'      => $event->id,
        'user_id'       => $user->id,
        'status'        => EventRegistrationStatus::Confirmed,
        'ical_token'    => 'token-mail-reminder',
        'registered_at' => now(),
    ]);

    $sevenDays = new EventReminderMail($user, $registration, '7d');
    $sevenDays->assertHasSubject('Dans 7 jours : Conférence Laravel — Laravel CI');

    $oneDay = new EventReminderMail($user, $registration, '1d');
    $oneDay->assertHasSubject('Demain : Conférence Laravel — Laravel CI');
});

it('builds the job alert mail with the matching offers', function () {
    $user  = User::factory()->create();
    $offer = JobOffer::factory()->active()->create(['title' => 'Développeur Laravel Senior']);

    $mail = new JobAlertMail($user, collect([$offer]));

    $mail->assertHasSubject('Nouvelles offres correspondant à votre profil — Laravel CI');
    $mail->assertSeeInHtml('Développeur Laravel Senior');
});

it('builds the mention mail with the author\'s name and the question title', function () {
    $mentioned = User::factory()->create();
    $author    = User::factory()->create(['name' => 'Fatou Bamba']);
    $question  = Question::factory()->for($author, 'user')->create(['title' => 'Comment utiliser les Form Requests ?']);

    $mail = new MentionMail($mentioned, $author, $question);

    $mail->assertHasSubject('Fatou Bamba vous a mentionné — Laravel CI');
    $mail->assertSeeInHtml('Comment utiliser les Form Requests ?');
});

it('builds the new answer mail with the answer body', function () {
    $user     = User::factory()->create();
    $question = Question::factory()->for($user, 'user')->create();
    $answer   = Answer::factory()->for($question)->create(['body' => 'Voici la solution à votre problème.']);

    $mail = new NewAnswerMail($user, $answer);

    $mail->assertHasSubject('Nouvelle réponse à votre question — Laravel CI');
    $mail->assertSeeInHtml('Voici la solution à votre problème.');
});

it('builds the new job application mail with the job offer title', function () {
    $company = Company::factory()->create();
    $account = makeCompanyAccount(['company_id' => $company->id]);
    $offer   = JobOffer::factory()->active()->for($company)->create(['title' => 'Lead Développeur Laravel']);
    $user    = User::factory()->create();

    $application = JobApplication::create([
        'job_offer_id' => $offer->id,
        'user_id'      => $user->id,
        'status'       => 'pending',
    ]);

    $mail = new NewJobApplicationMail($account, $application);

    $mail->assertHasSubject('Nouvelle candidature pour Lead Développeur Laravel — Laravel CI');
    $mail->assertSeeInHtml('Lead Développeur Laravel');
});
