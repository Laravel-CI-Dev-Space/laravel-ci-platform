<?php

declare(strict_types=1);

namespace App\Http\Controllers\Jobs;

use App\Http\Controllers\Controller;
use App\Http\Requests\Jobs\StoreJobApplicationRequest;
use App\Models\JobOffer;
use App\Models\User;
use App\Services\Jobs\JobApplicationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class JobApplicationController extends Controller
{
    public function __construct(private readonly JobApplicationService $applicationService) {}

    /**
     * Soumet une candidature pour une offre d'emploi.
     */
    public function store(StoreJobApplicationRequest $request, JobOffer $offer): RedirectResponse
    {
        abort_unless($offer->status === 'active', 422, 'Cette offre n\'est plus disponible.');

        /** @var User $user */
        $user = Auth::user();

        $this->applicationService->apply(
            user: $user,
            offer: $offer,
            data: $request->validated(),
            cvFile: $request->file('cv'),
        );

        return redirect()
            ->route('jobs.show', $offer->slug)
            ->with('success', 'Votre candidature a été envoyée avec succès. Bonne chance !');
    }
}
