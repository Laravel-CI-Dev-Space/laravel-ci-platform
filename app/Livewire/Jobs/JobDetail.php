<?php

declare(strict_types=1);

namespace App\Livewire\Jobs;

use App\Models\JobFavorite;
use App\Models\JobOffer;
use App\Models\User;
use App\Services\Jobs\JobApplicationService;
use App\Services\Jobs\JobOfferService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

class JobDetail extends Component
{
    public string $offerSlug = '';

    public bool $showApplicationForm = false;

    public function mount(string $slug): void
    {
        $this->offerSlug = $slug;
    }

    /**
     * Vérifie si l'utilisateur connecté a déjà postulé.
     */
    public function hasApplied(JobApplicationService $applicationService): bool
    {
        if (! Auth::check()) {
            return false;
        }

        $offer = JobOffer::where('slug', $this->offerSlug)->first();

        return $offer !== null && $applicationService->hasApplied(Auth::user(), $offer);
    }

    /**
     * Vérifie si l'offre est dans les favoris de l'utilisateur.
     */
    public function isFavorited(): bool
    {
        if (! Auth::check()) {
            return false;
        }

        $offer = JobOffer::where('slug', $this->offerSlug)->first();

        return $offer !== null && JobFavorite::where('user_id', Auth::id())
            ->where('job_offer_id', $offer->id)
            ->exists();
    }

    /**
     * Toggle le favori pour l'utilisateur connecté.
     */
    public function toggleFavorite(JobOfferService $jobOfferService): void
    {
        if (! Auth::check()) {
            $this->redirect(route('login'));

            return;
        }

        /** @var User $user */
        $user  = Auth::user();
        $offer = JobOffer::where('slug', $this->offerSlug)->firstOrFail();

        $jobOfferService->toggleFavorite($user, $offer);
    }

    public function render(JobOfferService $jobOfferService, JobApplicationService $applicationService): View
    {
        $offer         = $jobOfferService->getBySlug($this->offerSlug);
        $similarOffers = $jobOfferService->getSimilarOffers($offer);
        $hasApplied    = Auth::check() && $applicationService->hasApplied(Auth::user(), $offer);
        $favorited     = $this->isFavorited();

        return view('livewire.jobs.job-detail', compact('offer', 'similarOffers', 'hasApplied', 'favorited'));
    }
}
