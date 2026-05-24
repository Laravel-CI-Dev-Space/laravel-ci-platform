<?php

namespace App\Services;

use App\Mail\WelcomeMail;
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

    public function sendArticlePublished(User $_user, mixed $_article): void
    {
        // TODO: Mail::to($_user->email)->queue(new ArticlePublishedMail($_user, $_article));
    }

    public function sendEventConfirmation(User $_user, mixed $_event): void
    {
        // TODO: Mail::to($_user->email)->queue(new EventConfirmationMail($_user, $_event));
    }
}
