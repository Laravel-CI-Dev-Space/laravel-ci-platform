<?php

declare(strict_types=1);

namespace App\Livewire\JobBoard;

use App\Models\JobOffer;
use App\Models\User;
use App\Services\Jobs\JobFavoriteService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

class JobFavoriteToggle extends Component
{
    use AuthorizesRequests;

    #[Locked]
    public int $jobOfferId;

    public bool $isFavorited = false;

    public bool $animate = false;

    public string $variant = 'web';

    public function mount(int $jobOfferId, string $variant = 'web'): void
    {
        $this->jobOfferId = $jobOfferId;
        $this->variant    = $variant;
        $this->syncFavoriteState();
    }

    public function toggle(JobFavoriteService $jobFavoriteService): void
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            $this->redirect(route('login'));

            return;
        }

        $this->authorize('favorite', $this->jobOffer);

        $this->isFavorited = $jobFavoriteService->toggle($user, $this->jobOffer);
        $this->animate      = true;

        $this->dispatch(
            'app-toast',
            message: $this->isFavorited
                ? 'Offre ajoutée à vos favoris'
                : 'Offre retirée de vos favoris',
            type: $this->isFavorited ? 'success' : 'info',
        );
        $this->dispatch('job-favorite-toggled', jobOfferId: $this->jobOfferId, isFavorited: $this->isFavorited);

        $this->js('setTimeout(() => $wire.set("animate", false), 500)');
    }

    #[Computed]
    public function jobOffer(): JobOffer
    {
        return JobOffer::query()->findOrFail($this->jobOfferId);
    }

    private function syncFavoriteState(): void
    {
        $offer = JobOffer::query()->find($this->jobOfferId);

        if ($offer === null) {
            $this->isFavorited = false;

            return;
        }

        if (isset($offer->is_favorited)) {
            $this->isFavorited = (bool) $offer->is_favorited;

            return;
        }

        $user = auth()->user();

        if (! $user instanceof User) {
            $this->isFavorited = false;

            return;
        }

        $this->isFavorited = $offer->favorites()
            ->where('user_id', $user->id)
            ->exists();
    }

    public function render(): View
    {
        return view('livewire.job-board.job-favorite-toggle');
    }
}
