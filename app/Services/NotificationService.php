<?php

namespace App\Services;

use App\Mail\WelcomeMail;
use App\Models\Article;
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

    public function sendEventReminder(User $_user, mixed $_event): void
    {
        // TODO: Mail::to($_user->email)->queue(new EventReminderMail($_user, $_event));
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

    public function sendEventConfirmation(User $_user, mixed $_event): void
    {
        // TODO: Mail::to($_user->email)->queue(new EventConfirmationMail($_user, $_event));
    }
}
