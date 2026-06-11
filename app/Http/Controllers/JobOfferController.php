<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Jobs\ApplyToJobRequest;
use App\Http\Requests\Jobs\SubmitJobOfferRequest;
use App\Models\JobOffer;
use App\Queries\Jobs\JobOfferDetailQuery;
use App\Services\Jobs\JobOfferService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class JobOfferController extends Controller
{
    public function __construct(
        private readonly JobOfferService $jobOfferService,
    ) {}

    public function index(): View
    {
        $this->authorize('viewAny', JobOffer::class);

        return view('web.jobs.index');
    }

    public function show(JobOffer $jobOffer): View
    {
        $this->authorize('view', $jobOffer);

        $jobOffer = JobOfferDetailQuery::findBySlug($jobOffer->slug, auth()->id());

        return view('web.jobs.show', compact('jobOffer'));
    }

    public function create(): View
    {
        $this->authorize('create', JobOffer::class);

        return view('web.jobs.create');
    }

    public function store(SubmitJobOfferRequest $request): RedirectResponse
    {
        $this->jobOfferService->submit($request->validated(), $request->user());

        return redirect()
            ->route('jobs.index')
            ->with('success', 'Votre offre a été soumise et sera publiée après validation par l\'équipe.');
    }

    public function apply(ApplyToJobRequest $request, JobOffer $jobOffer): RedirectResponse
    {
        $this->jobOfferService->apply(
            $jobOffer,
            $request->user(),
            $request->validated('cover_letter'),
        );

        return redirect()
            ->route('jobs.show', $jobOffer)
            ->with('success', 'Votre candidature a bien été enregistrée.');
    }
}
