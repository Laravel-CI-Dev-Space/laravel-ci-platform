<?php

declare(strict_types=1);

namespace App\Services\Jobs;

use App\Models\JobApplication;
use App\Models\JobOffer;
use App\Models\User;
use App\Services\Analytics\AnalyticsService;
use App\Services\AssetService;
use App\Services\NotificationService;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class JobApplicationService
{
    public function __construct(
        private readonly AssetService $assetService,
        private readonly NotificationService $notificationService,
        private readonly AnalyticsService $analytics,
    ) {}

    /**
     * Soumet une candidature pour une offre d'emploi.
     * Upload le CV si fourni. Vérifie l'unicité. Incrémente le compteur.
     *
     * @throws ValidationException Si l'user a déjà postulé
     */
    public function apply(User $user, JobOffer $offer, array $data, mixed $cvFile = null): JobApplication
    {
        abort_if(
            $this->hasApplied($user, $offer),
            422,
            'Vous avez déjà postulé à cette offre.'
        );

        $cvPath = null;

        if ($cvFile !== null) {
            $cvPath = $this->assetService->upload($cvFile, 'cv', 'cv', $user->id);
        }

        $application = JobApplication::create([
            'job_offer_id'  => $offer->id,
            'user_id'       => $user->id,
            'cv_path'       => $cvPath,
            'cover_letter'  => $data['cover_letter']  ?? null,
            'portfolio_url' => $data['portfolio_url'] ?? null,
            'linkedin_url'  => $data['linkedin_url']  ?? null,
            'status'        => 'pending',
        ]);

        $offer->increment('applications_count');

        $this->analytics->trackEvent(
            type: 'job_application',
            userId: $user->id,
            entityType: 'job_offer',
            entityId: $offer->id,
            metadata: [
                'offer_title' => $offer->title,
                'company'     => $offer->company?->name,
            ],
        );

        // Notifie le compte entreprise si l'offre est liée à une entreprise
        if ($offer->company?->accounts()->first()) {
            $this->notificationService->sendNewApplication(
                $offer->company->accounts()->first(),
                $application,
            );
        }

        return $application;
    }

    /**
     * Vérifie si un utilisateur a déjà postulé à une offre.
     */
    public function hasApplied(User $user, JobOffer $offer): bool
    {
        return JobApplication::where('user_id', $user->id)
            ->where('job_offer_id', $offer->id)
            ->exists();
    }

    /**
     * Récupère les candidatures d'un utilisateur avec les offres associées.
     */
    public function getUserApplications(User $user): Collection
    {
        return JobApplication::where('user_id', $user->id)
            ->with(['jobOffer.company', 'jobOffer.categories'])
            ->latest()
            ->get();
    }
}
