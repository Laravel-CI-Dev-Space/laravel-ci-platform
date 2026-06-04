<?php

declare(strict_types=1);

namespace App\Http\Controllers\Company\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\StoreCompanyRegistrationRequest;
use App\Models\CompanyRegistrationRequest;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CompanyRegisterController extends Controller
{
    public function __construct(private readonly NotificationService $notificationService) {}

    /**
     * Affiche le formulaire d'inscription entreprise.
     */
    public function showRegistrationForm(): View
    {
        return view('company.auth.register');
    }

    /**
     * Enregistre une demande d'inscription entreprise (status: pending).
     * Notifie les admins et redirige avec un message de confirmation.
     */
    public function store(StoreCompanyRegistrationRequest $request): RedirectResponse
    {
        $registrationRequest = CompanyRegistrationRequest::create($request->validated());

        $this->notificationService->sendCompanyRegistrationRequest($registrationRequest);

        return redirect()->route('company.register')
            ->with('success', 'Votre demande a bien été soumise. Vous recevrez un email de confirmation sous 48h après validation par notre équipe.');
    }
}
