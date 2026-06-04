<?php

declare(strict_types=1);

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\CompanyAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Vue d'ensemble du dashboard entreprise.
     */
    public function index(): View
    {
        /** @var CompanyAccount $account */
        $account = Auth::guard('company')->user();

        $company = $account->company;

        $stats = [
            'active_offers'      => $company?->activeJobOffers()->count() ?? 0,
            'total_offers'       => $company?->jobOffers()->count()       ?? 0,
            'total_applications' => $company?->jobOffers()
                ->withCount('applications')
                ->get()
                ->sum('applications_count') ?? 0,
            'pending_applications' => $company?->jobOffers()
                ->with(['applications' => fn ($q) => $q->where('status', 'pending')])
                ->get()
                ->sum(fn ($offer) => $offer->applications->count()) ?? 0,
        ];

        $recentApplications = $company?->jobOffers()
            ->with(['applications' => fn ($q) => $q->with('user')->latest()->limit(5)])
            ->get()
            ->flatMap(fn ($offer) => $offer->applications)
            ->sortByDesc('created_at')
            ->take(5) ?? collect();

        $activeOffers = $company?->activeJobOffers()
            ->withCount('applications')
            ->latest('published_at')
            ->limit(5)
            ->get() ?? collect();

        return view('company.dashboard.overview', compact(
            'account', 'company', 'stats', 'recentApplications', 'activeOffers'
        ));
    }
}
