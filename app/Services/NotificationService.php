<?php

namespace App\Services;

use App\Mail\WelcomeMail;
use App\Models\Article;
use App\Models\CompanyAccount;
use App\Models\CompanyRegistrationRequest;
use App\Models\EventRegistration;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /** Queues the welcome email after registration. */
    public function sendWelcome(User $user): void
    {
        try {
            Mail::to($user->email)->queue(new WelcomeMail($user));
        } catch (\Exception $e) {
            Log::error("Welcome email failed for {$user->email}: {$e->getMessage()}");
        }
    }

    // Stub methods below — parameters prefixed with _ are intentionally unused until implemented.

    public function sendNewAnswer(User $_user, mixed $_question): void
    {
        // TODO: Mail::to($_user->email)->queue(new NewAnswerMail($_user, $_question));
    }

    /**
     * Envoie le rappel J-7 ou J-1 avant un événement.
     *
     * @param  string  $type  '7d' ou '1d'
     */
    public function sendEventReminder(User $user, EventRegistration $registration, string $type): void
    {
        try {
            // Mail::to($user->email)->send(new EventReminderMail($user, $registration, $type));
            // TODO: créer EventReminderMail en Sprint 2
            Log::info("Rappel {$type} envoyé à {$user->email} : {$registration->event->title}");
        } catch (\Exception $e) {
            Log::error("Notification event reminder échouée : {$e->getMessage()}");
        }
    }

    public function sendJobAlert(User $_user, mixed $_offer): void
    {
        // TODO: Mail::to($_user->email)->queue(new JobAlertMail($_user, $_offer));
    }

    /**
     * Notifie l'auteur que son article a été publié.
     */
    public function sendArticlePublished(User $user, Article $article): void
    {
        try {
            // Mail::to($user->email)->send(new ArticlePublishedMail($user, $article));
            // TODO: create ArticlePublishedMail in Sprint 2
            Log::info("Article publié notifié à {$user->email} : {$article->title}");
        } catch (\Exception $e) {
            Log::error("Notification article publié échouée : {$e->getMessage()}");
        }
    }

    /**
     * Envoie l'email de confirmation d'inscription à un événement.
     */
    public function sendEventConfirmation(User $user, EventRegistration $registration): void
    {
        try {
            // Mail::to($user->email)->send(new EventConfirmationMail($user, $registration));
            // TODO: créer EventConfirmationMail en Sprint 2
            Log::info("Confirmation event envoyée à {$user->email} : {$registration->event->title}");
        } catch (\Exception $e) {
            Log::error("Notification event confirmation échouée : {$e->getMessage()}");
        }
    }

    /**
     * Notifie les admins d'une nouvelle demande d'inscription entreprise.
     */
    public function sendCompanyRegistrationRequest(CompanyRegistrationRequest $request): void
    {
        try {
            // TODO: Mail::to(config('mail.admin_address'))->queue(new CompanyRegistrationRequestMail($request));
            Log::info("Demande entreprise reçue de {$request->email} — {$request->company_name}");
        } catch (\Exception $e) {
            Log::error("Notification demande entreprise échouée : {$e->getMessage()}");
        }
    }

    /**
     * Envoie les accès à l'entreprise après validation admin.
     */
    public function sendCompanyAccessCredentials(CompanyAccount $account, string $temporaryPassword): void
    {
        try {
            // TODO: Mail::to($account->email)->queue(new CompanyAccessCredentialsMail($account, $temporaryPassword));
            Log::info("Accès entreprise envoyés à {$account->email} — mot de passe temporaire : {$temporaryPassword}");
        } catch (\Exception $e) {
            Log::error("Notification accès entreprise échouée : {$e->getMessage()}");
        }
    }

    /**
     * Notifie l'entreprise du rejet de sa demande.
     */
    public function sendCompanyRegistrationRejected(CompanyRegistrationRequest $request, string $reason): void
    {
        try {
            // TODO: Mail::to($request->email)->queue(new CompanyRegistrationRejectedMail($request, $reason));
            Log::info("Refus demande entreprise notifié à {$request->email} : {$reason}");
        } catch (\Exception $e) {
            Log::error("Notification refus entreprise échouée : {$e->getMessage()}");
        }
    }

    /**
     * Notifie l'entreprise d'une nouvelle candidature.
     */
    public function sendNewApplication(CompanyAccount $account, JobApplication $application): void
    {
        try {
            // TODO: Mail::to($account->email)->queue(new NewApplicationMail($account, $application));
            Log::info("Nouvelle candidature notifiée à {$account->email} pour l'offre #{$application->job_offer_id}");
        } catch (\Exception $e) {
            Log::error("Notification candidature échouée : {$e->getMessage()}");
        }
    }
}
