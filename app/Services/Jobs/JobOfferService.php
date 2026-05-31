<?php

namespace App\Services\Jobs;

use App\Enums\Jobs\JobApplicationStatus;
use App\Enums\Jobs\JobOfferStatus;
use App\Models\Company;
use App\Models\JobApplication;
use App\Models\JobOffer;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class JobOfferService
{
    /**
     * Enregistre une candidature membre sur une offre active.
     */
    public function apply(JobOffer $offer, User $user, ?string $coverLetter = null): JobApplication
    {
        if ($offer->applicationFor($user) !== null) {
            throw ValidationException::withMessages([
                'job' => 'Vous avez déjà postulé à cette offre.',
            ]);
        }

        return JobApplication::create([
            'job_offer_id' => $offer->id,
            'user_id'      => $user->id,
            'cover_letter' => $coverLetter,
            'status'       => JobApplicationStatus::PENDING,
        ]);
    }

    /**
     * Soumission d'une offre par un membre (brouillon en attente de modération).
     *
     * @param  array{company_name: string, company_description?: string|null, title: string, description: string, location: string, type: string}  $data
     */
    public function submit(array $data, User $user): JobOffer
    {
        return DB::transaction(function () use ($data) {
            $company = Company::create([
                'name'        => $data['company_name'],
                'description' => $data['company_description'] ?? null,
            ]);

            return JobOffer::create([
                'company_id'  => $company->id,
                'title'       => $data['title'],
                'description' => $data['description'],
                'location'    => $data['location'],
                'type'        => $data['type'],
                'status'      => JobOfferStatus::DRAFT,
            ]);
        });
    }
}
