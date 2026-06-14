<?php

declare(strict_types=1);

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\CompanyAccount;
use App\Models\JobApplication;
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

        $applicationsQuery = JobApplication::whereHas(
            'jobOffer',
            fn ($q) => $q->where('company_id', $company?->id)
        );

        $stats = [
            'active_offers'        => $company?->activeJobOffers()->count() ?? 0,
            'total_offers'         => $company?->jobOffers()->count()       ?? 0,
            'total_applications'   => $company ? (clone $applicationsQuery)->count() : 0,
            'pending_applications' => $company ? (clone $applicationsQuery)->where('status', 'pending')->count() : 0,
        ];

        $recentApplications = $company
            ? (clone $applicationsQuery)->with(['user', 'jobOffer'])->latest()->limit(5)->get()
            : collect();

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
