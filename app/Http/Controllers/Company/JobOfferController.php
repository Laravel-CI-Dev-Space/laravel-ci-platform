<?php

declare(strict_types=1);

namespace App\Http\Controllers\Company;

use App\Enums\JobContractType;
use App\Enums\JobLevel;
use App\Http\Controllers\Controller;
use App\Models\CompanyAccount;
use App\Models\JobOffer;
use App\Models\JobOfferCategory;
use App\Models\JobSkill;
use App\Services\Jobs\JobOfferService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class JobOfferController extends Controller
{
    public function __construct(private readonly JobOfferService $jobOfferService) {}

    /**
     * Liste les offres d'emploi de l'entreprise connectée.
     */
    public function index(): View
    {
        /** @var CompanyAccount $account */
        $account = Auth::guard('company')->user();

        $offers = $account->company?->jobOffers()
            ->withCount('applications')
            ->latest()
            ->paginate(15) ?? collect();

        return view('company.dashboard.offers.index', compact('account', 'offers'));
    }

    /**
     * Formulaire de création d'une offre d'emploi.
     */
    public function create(): View
    {
        $categories = JobOfferCategory::orderBy('name')->get();
        $skills     = JobSkill::orderBy('name')->get();

        return view('company.dashboard.offers.create', compact('categories', 'skills'));
    }

    /**
     * Enregistre une nouvelle offre (status: pending - attend validation admin).
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title'          => ['required', 'string', 'max:200'],
            'description'    => ['required', 'string', 'min:100'],
            'contract_type'  => ['required', Rule::enum(JobContractType::class)],
            'level'          => ['required', Rule::enum(JobLevel::class)],
            'location'       => ['nullable', 'string', 'max:150'],
            'country'        => ['nullable', 'string', 'max:100'],
            'is_remote'      => ['boolean'],
            'is_hybrid'      => ['boolean'],
            'salary_min'     => ['nullable', 'integer', 'min:0'],
            'salary_max'     => ['nullable', 'integer', 'min:0', 'gte:salary_min'],
            'currency'       => ['string', 'max:10'],
            'salary_visible' => ['boolean'],
            'is_urgent'      => ['boolean'],
            'categories'     => ['required', 'array', 'min:1'],
            'categories.*'   => ['exists:job_offer_categories,id'],
            'skills'         => ['required', 'array', 'min:1', 'max:10'],
            'skills.*'       => ['exists:job_skills,id'],
        ]);

        /** @var CompanyAccount $account */
        $account = Auth::guard('company')->user();

        $this->jobOfferService->createOffer(
            data: $data,
            company: $account->company,
            user: null,
        );

        return redirect()->route('company.offers.index')
            ->with('success', 'Votre offre a été soumise pour validation. Elle sera publiée après examen par notre équipe.');
    }

    /**
     * Affiche le détail d'une offre avec ses candidatures.
     */
    public function show(JobOffer $offer): View
    {
        /** @var CompanyAccount $account */
        $account = Auth::guard('company')->user();

        abort_unless($offer->company_id === $account->company_id, 403);

        $offer->load(['applications.user', 'categories', 'skills']);

        return view('company.dashboard.offers.show', compact('account', 'offer'));
    }
}
