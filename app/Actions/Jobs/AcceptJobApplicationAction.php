<?php

declare(strict_types=1);

namespace App\Actions\Jobs;

use App\Enums\Jobs\JobApplicationStatus;
use App\Models\JobApplication;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class AcceptJobApplicationAction
{
    public function execute(JobApplication $application): JobApplication
    {
        if ($application->status !== JobApplicationStatus::PENDING) {
            throw ValidationException::withMessages([
                'status' => 'Seules les candidatures en attente peuvent être acceptées.',
            ]);
        }

        return DB::transaction(function () use ($application) {
            $application->update(['status' => JobApplicationStatus::ACCEPTED]);

            return $application->fresh(['jobOffer.company', 'user']);
        });
    }
}
