<?php

namespace App\Services;

use App\Enums\UserRole;
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
use App\Models\Comment;
use App\Models\CompanyAccount;
use App\Models\CompanyRegistrationRequest;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\JobApplication;
use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class NotificationService
{
    /**
     * Envoie l'email de bienvenue après inscription GitHub.
     */
    public function sendWelcome(User $user): void
    {
        $this->sendMail($user->email, new WelcomeMail($user));
    }

    /**
     * Notifie l'auteur d'une question qu'une réponse a été postée.
     */
    public function sendNewAnswer(User $user, Answer $answer): void
    {
        if (! $this->userWantsNotification($user, 'new_answer')) {
            return;
        }

        $this->sendMail($user->email, new NewAnswerMail($user, $answer));

        $this->createInAppNotification($user, 'new_answer', [
            'message' => "Nouvelle réponse à votre question : {$answer->question->title}",
            'url'     => route('forum.show', $answer->question->slug),
            'icon'    => 'fa-solid fa-comment',
        ]);
    }

    /**
     * Notifie l'auteur que son article a été publié.
     */
    public function sendArticlePublished(User $user, Article $article): void
    {
        if (! $this->userWantsNotification($user, 'article_published')) {
            return;
        }

        $this->sendMail($user->email, new ArticlePublishedMail($user, $article));

        $this->createInAppNotification($user, 'article_published', [
            'message' => "Votre article \"{$article->title}\" a été publié !",
            'url'     => route('blog.show', $article->slug),
            'icon'    => 'fa-solid fa-book-open',
        ]);
    }

    /**
     * Envoie la confirmation d'inscription à un événement.
     */
    public function sendEventConfirmation(User $user, EventRegistration $registration): void
    {
        $this->sendMail($user->email, new EventConfirmationMail($user, $registration));

        $this->createInAppNotification($user, 'event_confirmation', [
            'message' => "Inscription confirmée : {$registration->event->title}",
            'url'     => route('events.show', $registration->event->slug),
            'icon'    => 'fa-solid fa-calendar-check',
        ]);
    }

    /**
     * Envoie le rappel J-7 ou J-1 avant un événement.
     *
     * @param  string  $type  '7d' ou '1d'
     */
    public function sendEventReminder(User $user, EventRegistration $registration, string $type): void
    {
        if (! $this->userWantsNotification($user, 'event_reminder')) {
            return;
        }

        $this->sendMail($user->email, new EventReminderMail($user, $registration, $type));
    }

    /**
     * Envoie les accès à l'entreprise après validation admin.
     *
     * Les identifiants sont systématiquement consignés dans le journal
     * "company_access" (audit/secours), en plus de l'email envoyé.
     */
    public function sendCompanyAccessCredentials(CompanyAccount $account, string $temporaryPassword): void
    {
        $this->sendMail($account->email, new CompanyAccessMail($account, $temporaryPassword));

        Log::channel('company_access')->info('Accès entreprise généré', [
            'account_id'         => $account->id,
            'company_name'       => $account->company?->name,
            'contact_name'       => $account->fullName(),
            'email'              => $account->email,
            'temporary_password' => $temporaryPassword,
        ]);
    }

    /**
     * Notifie l'entreprise du rejet de sa demande.
     */
    public function sendCompanyRegistrationRejected(CompanyRegistrationRequest $request, string $reason): void
    {
        $this->sendMail($request->email, new CompanyRegistrationRejectedMail($request, $reason));
    }

    /**
     * Notifie l'entreprise d'une nouvelle candidature.
     */
    public function sendNewApplication(CompanyAccount $account, JobApplication $application): void
    {
        $this->sendMail($account->email, new NewJobApplicationMail($account, $application));
    }

    /**
     * Envoie une alerte emploi à un membre.
     */
    public function sendJobAlert(User $user, Collection $offers): void
    {
        if (! $this->userWantsNotification($user, 'job_alert')) {
            return;
        }

        if ($offers->isEmpty()) {
            return;
        }

        $this->sendMail($user->email, new JobAlertMail($user, $offers));
    }

    /**
     * Notifie un utilisateur qu'il a été mentionné via #username.
     */
    public function sendMention(User $mentionedUser, User $author, Model $source): void
    {
        if (! $this->userWantsNotification($mentionedUser, 'mention')) {
            return;
        }

        $this->sendMail($mentionedUser->email, new MentionMail($mentionedUser, $author, $source));

        $this->createInAppNotification($mentionedUser, 'mention', $this->buildMentionContext($author, $source));
    }

    /**
     * Notifie un participant que le récapitulatif d'un événement est disponible.
     */
    public function sendEventRecapPublished(User $user, Event $event): void
    {
        if (! $this->userWantsNotification($user, 'event_recap')) {
            return;
        }

        $this->sendMail($user->email, new EventRecapPublishedMail($user, $event));

        $this->createInAppNotification($user, 'event_recap', [
            'message' => "Le récapitulatif de \"{$event->title}\" est disponible !",
            'url'     => route('events.show', $event->slug) . '#recap',
            'icon'    => 'fa-solid fa-images',
        ]);
    }

    /**
     * Notifie les admins d'une nouvelle demande entreprise.
     */
    public function sendCompanyRegistrationRequest(CompanyRegistrationRequest $request): void
    {
        $this->notifyAdmins('company_registration', [
            'message' => "Nouvelle demande entreprise : {$request->company_name}",
            'url'     => '/admin/company-registrations',
            'icon'    => 'fa-solid fa-building',
        ]);
    }

    /**
     * Crée une notification in-app pour tous les admins et super-admins.
     * Insertion en masse (une seule requête) plutôt qu'une par admin.
     */
    public function notifyAdmins(string $type, array $data): void
    {
        $adminIds = User::role([UserRole::Admin->value, UserRole::SuperAdmin->value])->pluck('id');

        if ($adminIds->isEmpty()) {
            return;
        }

        $now = now();

        try {
            DB::table('notifications')->insert(
                $adminIds->map(fn (int $adminId) => [
                    'id'              => (string) Str::uuid(),
                    'type'            => 'App\\Notifications\\' . str($type)->studly(),
                    'notifiable_type' => User::class,
                    'notifiable_id'   => $adminId,
                    'data'            => json_encode($data),
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ])->all()
            );
        } catch (\Exception $e) {
            Log::error("Notification in-app (admins) échouée : {$e->getMessage()}");
        }
    }

    /**
     * Crée une notification in-app (table notifications Laravel).
     */
    private function createInAppNotification(User $user, string $type, array $data): void
    {
        try {
            $user->notifications()->create([
                'id'              => Str::uuid(),
                'type'            => 'App\\Notifications\\' . str($type)->studly(),
                'notifiable_type' => User::class,
                'notifiable_id'   => $user->id,
                'data'            => $data,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        } catch (\Exception $e) {
            Log::error("Notification in-app échouée : {$e->getMessage()}");
        }
    }

    /**
     * Construit le contenu de la notification in-app pour une mention.
     */
    private function buildMentionContext(User $author, Model $source): array
    {
        return match (true) {
            $source instanceof Question => [
                'message' => "{$author->name} vous a mentionné dans la question « {$source->title} »",
                'url'     => route('forum.show', $source->slug),
                'icon'    => 'fa-solid fa-hashtag',
            ],
            $source instanceof Answer => [
                'message' => "{$author->name} vous a mentionné dans une réponse à « {$source->question->title} »",
                'url'     => route('forum.show', $source->question->slug) . '#answer-' . $source->id,
                'icon'    => 'fa-solid fa-hashtag',
            ],
            $source instanceof Comment => [
                'message' => "{$author->name} vous a mentionné dans un commentaire",
                'url'     => $this->resolveCommentUrl($source),
                'icon'    => 'fa-solid fa-hashtag',
            ],
            default => [
                'message' => "{$author->name} vous a mentionné",
                'url'     => route('home'),
                'icon'    => 'fa-solid fa-hashtag',
            ],
        };
    }

    /**
     * Résout l'URL vers le contenu auquel un commentaire appartient.
     */
    public function resolveCommentUrl(Comment $comment): string
    {
        $commentable = $comment->commentable;

        return match (true) {
            $commentable instanceof Question => route('forum.show', $commentable->slug) . '#comment-' . $comment->id,
            $commentable instanceof Answer   => route('forum.show', $commentable->question->slug) . '#comment-' . $comment->id,
            $commentable instanceof Article  => route('blog.show', $commentable->slug) . '#comment-' . $comment->id,
            default                          => route('home'),
        };
    }

    /**
     * Vérifie si l'utilisateur souhaite recevoir ce type de notification.
     */
    private function userWantsNotification(User $user, string $type): bool
    {
        $prefs = $user->profile?->notification_preferences ?? [];

        if (empty($prefs)) {
            return true; // par défaut tout activé
        }

        return $prefs[$type] ?? true;
    }

    /**
     * Met l'email en file d'attente (les mailables implémentent ShouldQueue),
     * pour ne pas bloquer la requête en cours sur l'envoi SMTP.
     */
    private function sendMail(string $to, $mailable): void
    {
        try {
            Mail::to($to)->queue($mailable);
        } catch (\Exception $e) {
            Log::error("Mise en file de l'email échouée vers {$to} : {$e->getMessage()}");
        }
    }
}
