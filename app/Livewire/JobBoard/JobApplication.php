<?php

declare(strict_types=1);

namespace App\Livewire\JobBoard;

use App\Models\JobApplication as JobApplicationModel;
use App\Models\JobOffer;
use App\Models\User;
use App\Queries\Jobs\JobOfferDetailQuery;
use App\Services\Jobs\JobOfferService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

class JobApplication extends Component
{
    use AuthorizesRequests;

    #[Locked]
    public int $jobOfferId;

    public string $coverLetter = '';

    public ?string $flashMessage = null;

    public function mount(int $jobOfferId): void
    {
        $this->jobOfferId = $jobOfferId;
    }

    public function apply(JobOfferService $jobOfferService): void
    {
        $offer = $this->jobOffer;
        $user  = auth()->user();

        if (! $user instanceof User) {
            $this->redirect(route('login'));

            return;
        }

        $this->authorize('apply', $offer);

        $this->validate([
            'coverLetter' => ['nullable', 'string', 'max:5000'],
        ]);

        try {
            $jobOfferService->apply($offer, $user, $this->coverLetter !== '' ? $this->coverLetter : null);
            $this->flashMessage = 'Votre candidature a bien été enregistrée.';
            $this->coverLetter  = '';
        } catch (ValidationException $e) {
            $this->addError('apply', $e->validator->errors()->first() ?? 'Candidature impossible.');
        }

        unset($this->jobOffer, $this->application, $this->canApply);
    }

    #[Computed]
    public function jobOffer(): JobOffer
    {
        return JobOfferDetailQuery::findById($this->jobOfferId, auth()->id());
    }

    #[Computed]
    public function application(): ?JobApplicationModel
    {
        return $this->jobOffer->applicationFor(auth()->user());
    }

    #[Computed]
    public function canApply(): bool
    {
        $user = auth()->user();

        return $user !== null && $user->can('apply', $this->jobOffer);
    }

    public function render(): View
    {
        return view('livewire.job-board.job-application');
    }
}
