<?php

namespace App\Services;

use App\Mail\EventConfirmationMail;
use App\Mail\WelcomeMail;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * Envoie l'email de bienvenue après inscription.
     * Appelé dans SocialiteService::createUser()
     */
    public function sendWelcome(User $user): void
    {
        try {
            Mail::to($user->email)->send(new WelcomeMail($user));
        } catch (\Exception $e) {
            // On log l'erreur mais on ne bloque pas l'inscription
            Log::error("Email bienvenue échoué pour {$user->email} : {$e->getMessage()}");
        }
    }

    /**
     * Envoie une notification de nouvelle réponse sur le forum.
     * Appelé par Abdoul dans QuestionService
     */
    public function sendNewAnswer(User $user, $question): void
    {
        // À implémenter par Emmanuel — module notifications
        // Mail::to($user->email)->send(new NewAnswerMail($user, $question));
    }

    /**
     * Envoie un rappel d'événement.
     * Appelé par Roger dans EventService
     */
    public function sendEventReminder(User $user, $event): void
    {
        // À implémenter par Emmanuel — module notifications
        // Mail::to($user->email)->send(new EventReminderMail($user, $event));
    }

    /**
     * Envoie une alerte emploi.
     * Appelé par Roger dans JobOfferService
     */
    public function sendJobAlert(User $user, $offer): void
    {
        // À implémenter par Emmanuel — module notifications
        // Mail::to($user->email)->send(new JobAlertMail($user, $offer));
    }

    /**
     * Envoie une notification de publication d'article.
     * Appelé par Emmanuel dans ArticleService
     */
    public function sendArticlePublished(User $user, $article): void
    {
        // À implémenter par Emmanuel — module notifications
        // Mail::to($user->email)->send(new ArticlePublishedMail($user, $article));
    }

    /**
     * Envoie une confirmation d'inscription à un événement.
     * Appelé par Roger dans EventService
     */
    public function sendEventConfirmation(User $user, Event $event): void
    {
        try {
            Mail::to($user->email)->send(new EventConfirmationMail($user, $event));
        } catch (\Exception $e) {
            Log::error("Email confirmation event échoué pour {$user->email} : {$e->getMessage()}");
        }
    }
}
