<?php

declare(strict_types=1);

namespace App\Livewire\JobBoard;

use App\Enums\Jobs\JobOfferType;
use App\Models\JobOffer;
use App\Services\Jobs\JobOfferService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class SubmitJobOffer extends Component
{
    use AuthorizesRequests;

    public string $company_name = '';

    public string $company_description = '';

    public string $title = '';

    public string $description = '';

    public string $location = '';

    public string $type = '';

    public function mount(): void
    {
        $this->authorize('create', JobOffer::class);
    }

    public function submit(JobOfferService $jobOfferService): void
    {
        $this->authorize('create', JobOffer::class);

        $validated = $this->validate([
            'company_name'        => ['required', 'string', 'max:255'],
            'company_description' => ['nullable', 'string', 'max:2000'],
            'title'               => ['required', 'string', 'max:255'],
            'description'         => ['required', 'string', 'min:50'],
            'location'            => ['required', 'string', 'max:255'],
            'type'                => ['required', Rule::enum(JobOfferType::class)],
        ]);

        $jobOfferService->submit($validated, auth()->user());

        session()->flash('success', 'Votre offre a été soumise et sera publiée après validation par l\'équipe.');

        $this->redirect(route('jobs.index'), navigate: true);
    }

    /**
     * @return list<JobOfferType>
     */
    #[Computed]
    public function offerTypes(): array
    {
        return JobOfferType::cases();
    }

    public function render(): View
    {
        return view('livewire.job-board.submit-job-offer');
    }
}
