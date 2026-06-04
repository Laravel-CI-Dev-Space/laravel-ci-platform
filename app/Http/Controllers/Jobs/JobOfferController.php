<?php

declare(strict_types=1);

namespace App\Http\Controllers\Jobs;

use App\Http\Controllers\Controller;
use App\Models\JobOffer;
use App\Models\User;
use App\Services\Jobs\JobOfferService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class JobOfferController extends Controller
{
    public function __construct(private readonly JobOfferService $jobOfferService) {}

    /**
     * Liste paginée des offres actives.
     */
    public function index(): View
    {
        return view('web.jobs.index');
    }

    /**
     * Affiche le détail d'une offre par son slug.
     */
    public function show(string $slug): View
    {
        $offer = $this->jobOfferService->getBySlug($slug);
        $this->jobOfferService->incrementViews($offer);

        return view('web.jobs.show', compact('offer', 'slug'));
    }

    /**
     * Toggle favori pour un membre connecté.
     */
    public function toggleFavorite(JobOffer $offer): RedirectResponse|JsonResponse
    {
        /** @var User $user */
        $user    = Auth::user();
        $added   = $this->jobOfferService->toggleFavorite($user, $offer);
        $message = $added ? 'Offre sauvegardée dans vos favoris.' : 'Offre retirée de vos favoris.';

        if (request()->wantsJson()) {
            return response()->json(['added' => $added, 'message' => $message]);
        }

        return back()->with('success', $message);
    }
}
