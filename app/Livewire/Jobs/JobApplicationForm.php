<?php

declare(strict_types=1);

namespace App\Livewire\Jobs;

use App\Models\JobOffer;
use App\Models\User;
use App\Services\Jobs\JobApplicationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;

class JobApplicationForm extends Component
{
    use WithFileUploads;

    public int $jobOfferId = 0;

    public string $coverLetter = '';

    public mixed $cv = null;

    public string $portfolioUrl = '';

    public string $linkedinUrl = '';

    public bool $submitted = false;

    public function mount(JobOffer $offer): void
    {
        $this->jobOfferId = $offer->id;
    }

    protected function rules(): array
    {
        return [
            'coverLetter'  => ['nullable', 'string', 'min:50', 'max:3000'],
            'cv'           => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
            'portfolioUrl' => ['nullable', 'url', 'max:255'],
            'linkedinUrl'  => ['nullable', 'url', 'max:255'],
        ];
    }

    protected function messages(): array
    {
        return [
            'coverLetter.min'  => 'La lettre de motivation doit contenir au moins :min caractères.',
            'coverLetter.max'  => 'La lettre de motivation ne peut pas dépasser :max caractères.',
            'cv.file'          => 'Le fichier CV est invalide.',
            'cv.mimes'         => 'Le CV doit être au format PDF, DOC ou DOCX.',
            'cv.max'           => 'Le CV ne peut pas dépasser 5 Mo.',
            'portfolioUrl.url' => "L'URL du portfolio est invalide.",
            'linkedinUrl.url'  => "L'URL LinkedIn est invalide.",
        ];
    }

    /**
     * Soumet la candidature via JobApplicationService.
     */
    public function submit(JobApplicationService $applicationService): void
    {
        $this->validate();

        if (empty($this->coverLetter) && $this->cv === null) {
            $this->addError('cv', 'Veuillez fournir au moins un CV ou une lettre de motivation.');

            return;
        }

        /** @var User $user */
        $user  = Auth::user();
        $offer = JobOffer::findOrFail($this->jobOfferId);

        $applicationService->apply(
            user: $user,
            offer: $offer,
            data: [
                'cover_letter'  => $this->coverLetter ?: null,
                'portfolio_url' => $this->portfolioUrl ?: null,
                'linkedin_url'  => $this->linkedinUrl ?: null,
            ],
            cvFile: $this->cv,
        );

        $this->submitted   = true;
        $this->coverLetter = '';
        $this->cv          = null;
    }

    public function render(): View
    {
        return view('livewire.jobs.job-application-form');
    }
}
