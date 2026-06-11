<?php

declare(strict_types=1);

namespace App\Livewire\Dashboard;

use App\Models\JobOffer;
use App\Models\User;
use App\Queries\Jobs\JobFavoriteListQuery;
use App\Services\Jobs\JobFavoriteService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class MemberJobFavorites extends Component
{
    use AuthorizesRequests;

    public ?int $jobOfferIdToRemove = null;

    /**
     * @return Collection<int, JobOffer>
     */
    #[Computed]
    public function favorites(): Collection
    {
        /** @var User $user */
        $user = auth()->user();

        return JobFavoriteListQuery::forUser($user)
            ->get()
            ->map(fn ($favorite) => $favorite->jobOffer)
            ->filter();
    }

    #[On('job-favorite-toggled')]
    public function refreshFavorites(): void
    {
        unset($this->favorites);
    }

    #[Computed]
    public function jobOfferToRemove(): ?JobOffer
    {
        if ($this->jobOfferIdToRemove === null) {
            return null;
        }

        return JobOffer::query()
            ->with('company')
            ->find($this->jobOfferIdToRemove);
    }

    public function openRemoveModal(int $jobOfferId): void
    {
        $this->jobOfferIdToRemove = $jobOfferId;
    }

    public function closeRemoveModal(): void
    {
        $this->jobOfferIdToRemove = null;
        unset($this->jobOfferToRemove);
    }

    public function confirmRemove(JobFavoriteService $jobFavoriteService): void
    {
        if ($this->jobOfferIdToRemove === null) {
            return;
        }

        $this->remove($this->jobOfferIdToRemove, $jobFavoriteService);
        $this->closeRemoveModal();
    }

    public function remove(int $jobOfferId, JobFavoriteService $jobFavoriteService): void
    {
        /** @var User $user */
        $user  = auth()->user();
        $offer = JobOffer::query()->findOrFail($jobOfferId);

        $this->authorize('favorite', $offer);

        if ($jobFavoriteService->isFavorited($user, $offer)) {
            $jobFavoriteService->toggle($user, $offer);
        }

        unset($this->favorites);

        $this->dispatch('app-toast', message: 'Offre retirée de vos favoris', type: 'info');
        $this->dispatch('job-favorite-toggled', jobOfferId: $jobOfferId, isFavorited: false);
    }

    public function render(): View
    {
        return view('livewire.dashboard.member-job-favorites');
    }
}
