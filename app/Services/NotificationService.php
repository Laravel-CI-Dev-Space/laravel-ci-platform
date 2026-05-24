<?php

namespace App\Services;

use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * Envoie l'email de bienvenue en file d'attente après inscription.
     */
    public function sendWelcome(User $user): void
    {
        try {
            Mail::to($user->email)->queue(new WelcomeMail($user));
        } catch (\Exception $e) {
            Log::error("Email bienvenue échoué pour {$user->email} : {$e->getMessage()}");
        }
    }

    public function sendNewAnswer(User $user, mixed $question): void
    {
        // TODO: implémenter — Mail::to($user->email)->queue(new NewAnswerMail($user, $question));
    }

    public function sendEventReminder(User $user, mixed $event): void
    {
        // TODO: implémenter — Mail::to($user->email)->queue(new EventReminderMail($user, $event));
    }

    public function sendJobAlert(User $user, mixed $offer): void
    {
        // TODO: implémenter — Mail::to($user->email)->queue(new JobAlertMail($user, $offer));
    }

    public function sendArticlePublished(User $user, mixed $article): void
    {
        // TODO: implémenter — Mail::to($user->email)->queue(new ArticlePublishedMail($user, $article));
    }

    public function sendEventConfirmation(User $user, mixed $event): void
    {
        // TODO: implémenter — Mail::to($user->email)->queue(new EventConfirmationMail($user, $event));
    }
}
