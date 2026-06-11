<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Answer;
use App\Models\Article;
use App\Models\Company;
use App\Models\CompanyAccount;
use App\Models\CompanyRegistrationRequest;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\JobApplication;
use App\Models\JobOffer;
use App\Models\Question;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Crée les données minimales nécessaires et déclenche l'envoi de tous les
 * emails du module Notifications, pour vérifier la configuration Mailtrap.
 *
 * Usage : php artisan db:seed --class=NotificationMailTestSeeder
 */
class NotificationMailTestSeeder extends Seeder
{
    private const TEST_EMAIL = 'notifications-test@laravelci.com';

    public function run(): void
    {
        $service = app(NotificationService::class);

        $user   = $this->testUser();
        $author = User::where('id', '!=', $user->id)->first() ?? $user;

        // 1. Bienvenue
        $service->sendWelcome($user);
        $this->pause();

        // 2. Nouvelle réponse à une question
        $question = Question::firstOrCreate(
            ['slug' => 'question-test-notifications'],
            [
                'user_id' => $user->id,
                'title'   => 'Comment tester les notifications par email ?',
                'body'    => 'Ceci est une question de test générée par NotificationMailTestSeeder.',
                'status'  => 'published',
            ]
        );

        $answer = Answer::firstOrCreate(
            ['question_id' => $question->id, 'user_id' => $author->id],
            ['body' => "Voici une réponse de test. Vérifie que l'email 'Nouvelle réponse' arrive bien sur Mailtrap."]
        );

        $service->sendNewAnswer($user, $answer);
        $this->pause();

        // 3. Article publié
        $article = Article::firstOrCreate(
            ['slug' => 'article-test-notifications'],
            [
                'user_id'      => $user->id,
                'title'        => 'Article de test pour les notifications',
                'excerpt'      => 'Article généré par NotificationMailTestSeeder.',
                'body'         => 'Contenu de test pour vérifier l\'email de publication d\'article.',
                'level'        => 'beginner',
                'status'       => 'published',
                'published_at' => now(),
            ]
        );

        $service->sendArticlePublished($user, $article);
        $this->pause();

        // 4 & 5. Confirmation + rappels d'événement
        $event = Event::first() ?? Event::create([
            'created_by'  => $author->id,
            'title'       => 'Événement de test',
            'slug'        => 'evenement-test-notifications',
            'description' => 'Événement généré par NotificationMailTestSeeder.',
            'type'        => 'meetup',
            'starts_at'   => now()->addWeek(),
            'ends_at'     => now()->addWeek()->addHours(2),
            'status'      => 'published',
        ]);

        $registration = EventRegistration::firstOrCreate(
            ['event_id' => $event->id, 'user_id' => $user->id],
            [
                'status'        => 'confirmed',
                'ical_token'    => Str::random(64),
                'registered_at' => now(),
                'ticket_number' => 'TEST-' . Str::upper(Str::random(8)),
            ]
        );

        $service->sendEventConfirmation($user, $registration);
        $this->pause();
        $service->sendEventReminder($user, $registration, '7d');
        $this->pause();
        $service->sendEventReminder($user, $registration, '1d');
        $this->pause();

        // 6. Accès entreprise (login + mot de passe temporaire)
        $company = Company::firstOrCreate(
            ['slug' => 'entreprise-test-notifications'],
            [
                'name'        => 'Entreprise Test',
                'email'       => self::TEST_EMAIL,
                'is_verified' => true,
            ]
        );

        $account = CompanyAccount::firstOrCreate(
            ['email' => self::TEST_EMAIL],
            [
                'company_id' => $company->id,
                'first_name' => 'Test',
                'last_name'  => 'Notifications',
                'password'   => Hash::make('Test#1234'),
                'position'   => 'Responsable RH',
                'status'     => 'active',
            ]
        );

        $service->sendCompanyAccessCredentials($account, 'Test#1234');
        $this->pause();

        // 7. Demande d'inscription entreprise rejetée
        $registrationRequest = CompanyRegistrationRequest::firstOrCreate(
            ['email' => 'demande-rejetee-test@laravelci.com'],
            [
                'first_name'      => 'Test',
                'last_name'       => 'Rejet',
                'company_name'    => 'Entreprise Rejetée Test',
                'position'        => 'Fondateur',
                'business_domain' => 'Technologies',
                'status'          => 'rejected',
            ]
        );

        $service->sendCompanyRegistrationRejected(
            $registrationRequest,
            'Les informations fournies ne permettent pas de vérifier l\'existence de l\'entreprise.'
        );
        $this->pause();

        // 8. Nouvelle candidature
        $jobOffer = JobOffer::firstOrCreate(
            ['slug' => 'offre-test-notifications'],
            [
                'company_id'    => $company->id,
                'posted_by'     => $author->id,
                'title'         => 'Développeur Laravel (offre de test)',
                'description'   => 'Offre générée par NotificationMailTestSeeder.',
                'contract_type' => 'cdi',
                'level'         => 'any',
                'status'        => 'active',
                'published_at'  => now(),
            ]
        );

        $application = JobApplication::firstOrCreate(
            ['job_offer_id' => $jobOffer->id, 'user_id' => $user->id]
        );

        $service->sendNewApplication($account, $application);
        $this->pause();

        // 9. Alerte emploi
        $service->sendJobAlert($user, collect([$jobOffer]));
        $this->pause();

        $this->command?->info('Emails de test envoyés vers Mailtrap (destinataire : ' . self::TEST_EMAIL . ' / ' . $user->email . ').');
    }

    /**
     * Pause entre deux envois pour respecter la limite de débit du sandbox Mailtrap.
     */
    private function pause(): void
    {
        sleep(6);
    }

    /**
     * Récupère ou crée l'utilisateur de test (rôle membre).
     */
    private function testUser(): User
    {
        $user = User::firstOrCreate(
            ['email' => self::TEST_EMAIL],
            [
                'name'              => 'Testeur Notifications',
                'github_id'         => 'test-notifications-' . Str::random(8),
                'github_username'   => 'test-notifications',
                'is_active'         => true,
                'email_verified_at' => now(),
            ]
        );

        if (! $user->hasRole(UserRole::Member->value)) {
            $user->assignRole(UserRole::Member->value);
        }

        return $user;
    }
}
