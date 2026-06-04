<?php

declare(strict_types=1);

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\CompanyAccount;
use App\Models\JobApplication;
use App\Models\JobOffer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ApplicationController extends Controller
{
    /**
     * Liste les candidatures pour une offre spécifique.
     */
    public function index(JobOffer $offer): View
    {
        /** @var CompanyAccount $account */
        $account = Auth::guard('company')->user();

        abort_unless($offer->company_id === $account->company_id, 403);

        $applications = $offer->applications()
            ->with('user')
            ->latest()
            ->paginate(20);

        return view('company.dashboard.applications.index', compact('account', 'offer', 'applications'));
    }

    /**
     * Affiche une candidature et la marque comme vue.
     */
    public function show(JobApplication $application): View
    {
        /** @var CompanyAccount $account */
        $account = Auth::guard('company')->user();

        abort_unless($application->jobOffer->company_id === $account->company_id, 403);

        if ($application->viewed_at === null) {
            $application->update(['viewed_at' => now(), 'status' => 'viewed']);
        }

        $application->load('user', 'jobOffer');

        return view('company.dashboard.applications.show', compact('account', 'application'));
    }

    /**
     * Met à jour le statut d'une candidature (shortlisted / accepted / rejected).
     */
    public function updateStatus(Request $request, JobApplication $application): RedirectResponse
    {
        /** @var CompanyAccount $account */
        $account = Auth::guard('company')->user();

        abort_unless($application->jobOffer->company_id === $account->company_id, 403);

        $data = $request->validate([
            'status'        => ['required', 'in:viewed,shortlisted,accepted,rejected'],
            'employer_note' => ['nullable', 'string', 'max:1000'],
        ], [
            'status.required' => 'Le statut est obligatoire.',
            'status.in'       => 'Statut invalide.',
        ]);

        $application->update($data);

        return back()->with('success', 'Statut de la candidature mis à jour.');
    }

    /**
     * Retourne le CV du candidat en téléchargement.
     */
    public function downloadCv(JobApplication $application): BinaryFileResponse
    {
        /** @var CompanyAccount $account */
        $account = Auth::guard('company')->user();

        abort_unless($application->jobOffer->company_id === $account->company_id, 403);
        abort_unless($application->cv_path !== null, 404, 'Aucun CV fourni pour cette candidature.');

        $path = public_path('assets/cv/' . $application->cv_path);

        abort_unless(file_exists($path), 404, 'Fichier CV introuvable.');

        return response()->download($path);
    }
}
