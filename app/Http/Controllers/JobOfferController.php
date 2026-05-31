<?php

namespace App\Http\Controllers;

use App\Enums\Jobs\JobOfferType;
use App\Http\Requests\Jobs\ApplyToJobRequest;
use App\Http\Requests\Jobs\SubmitJobOfferRequest;
use App\Models\JobCategory;
use App\Models\JobOffer;
use App\Models\JobSkill;
use App\Services\Jobs\JobOfferService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JobOfferController extends Controller
{
    public function __construct(
        private readonly JobOfferService $jobOfferService,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', JobOffer::class);

        $query = JobOffer::query()
            ->with(['company', 'category', 'skills'])
            ->active();

        $type = $request->string('type')->toString() ?: null;
        if ($type && JobOfferType::tryFrom($type)) {
            $query->where('type', $type);
        }

        if ($request->boolean('remote')) {
            $query->where('type', JobOfferType::REMOTE);
        }

        $skillSlug = $request->string('skill')->toString() ?: null;
        if ($skillSlug) {
            $query->whereHas('skills', fn ($q) => $q->where('slug', $skillSlug));
        }

        $categorySlug = $request->string('category')->toString() ?: null;
        if ($categorySlug) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $categorySlug));
        }

        $sort  = $request->string('sort', 'newest')->toString();
        $query = match ($sort) {
            'title' => $query->orderBy('title'),
            default => $query->orderByDesc('created_at'),
        };

        $offers = $query->paginate(12)->withQueryString();

        $categories = JobCategory::query()->orderBy('name')->get();
        $skills     = JobSkill::query()->orderBy('name')->get();

        return view('jobs.index', [
            'offers'     => $offers,
            'categories' => $categories,
            'skills'     => $skills,
            'type'       => $type,
            'skill'      => $skillSlug,
            'category'   => $categorySlug,
            'remote'     => $request->boolean('remote'),
            'sort'       => $sort,
        ]);
    }

    public function show(JobOffer $jobOffer): View
    {
        $this->authorize('view', $jobOffer);

        $jobOffer->load(['company', 'category', 'skills']);

        $user        = auth()->user();
        $application = $jobOffer->applicationFor($user);
        $canApply    = $user?->can('apply', $jobOffer) ?? false;

        return view('jobs.show', compact('jobOffer', 'application', 'canApply'));
    }

    public function create(): View
    {
        $this->authorize('create', JobOffer::class);

        return view('jobs.create');
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
