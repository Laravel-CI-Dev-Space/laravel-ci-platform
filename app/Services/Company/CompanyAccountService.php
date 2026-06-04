<?php

declare(strict_types=1);

namespace App\Services\Company;

use App\Models\Company;
use App\Models\CompanyAccount;
use App\Models\CompanyRegistrationRequest;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Str;

class CompanyAccountService
{
    public function __construct(private readonly NotificationService $notificationService) {}

    /**
     * Approuve une demande d'inscription entreprise.
     * Crée la Company, crée le CompanyAccount, envoie les accès par email.
     */
    public function approveRegistrationRequest(
        User $admin,
        CompanyRegistrationRequest $request,
    ): CompanyAccount {
        // Créer l'entreprise
        $company = Company::create([
            'submitted_by' => $admin->id,
            'name'         => $request->company_name,
            'slug'         => $this->generateCompanySlug($request->company_name),
            'country'      => $request->country,
            'city'         => $request->city,
            'website'      => $request->website,
            'is_verified'  => true,
        ]);

        $temporaryPassword = $this->generateTemporaryPassword();

        // Créer le compte entreprise
        $account = CompanyAccount::create([
            'company_id'          => $company->id,
            'first_name'          => $request->first_name,
            'last_name'           => $request->last_name,
            'email'               => $request->email,
            'password'            => $temporaryPassword,
            'position'            => $request->position,
            'phone'               => $request->phone,
            'status'              => 'active',
            'password_changed_at' => null, // Force le changement au 1er login
        ]);

        // Marquer la demande comme approuvée
        $request->update([
            'status'      => 'approved',
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
        ]);

        // Envoyer les accès à l'entreprise
        $this->notificationService->sendCompanyAccessCredentials($account, $temporaryPassword);

        return $account;
    }

    /**
     * Rejette une demande d'inscription avec une raison.
     * Envoie un email de refus à l'entreprise.
     */
    public function rejectRegistrationRequest(
        User $admin,
        CompanyRegistrationRequest $request,
        string $reason,
    ): void {
        $request->update([
            'status'           => 'rejected',
            'rejection_reason' => $reason,
            'reviewed_by'      => $admin->id,
            'reviewed_at'      => now(),
        ]);

        $this->notificationService->sendCompanyRegistrationRejected($request, $reason);
    }

    /**
     * Suspend un compte entreprise avec une raison.
     */
    public function suspend(CompanyAccount $account, string $reason): void
    {
        $account->update([
            'status'           => 'suspended',
            'rejection_reason' => $reason,
        ]);
    }

    /**
     * Réactive un compte entreprise suspendu.
     */
    public function reactivate(CompanyAccount $account): void
    {
        $account->update([
            'status'           => 'active',
            'rejection_reason' => null,
        ]);
    }

    /**
     * Génère un mot de passe temporaire mémorisable.
     * Format : LCI-XXXX-XXXX (lettres majuscules + chiffres)
     */
    private function generateTemporaryPassword(): string
    {
        $part1 = strtoupper(Str::random(4));
        $part2 = strtoupper(Str::random(4));

        return "LCI-{$part1}-{$part2}";
    }

    private function generateCompanySlug(string $name): string
    {
        $slug     = Str::slug($name);
        $original = $slug;
        $count    = 1;

        while (Company::where('slug', $slug)->exists()) {
            $slug = "{$original}-{$count}";
            $count++;
        }

        return $slug;
    }
}
