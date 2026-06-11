<?php

declare(strict_types=1);

namespace App\Actions\Jobs;

use App\Enums\Jobs\JobApplicationStatus;
use App\Models\JobApplication;
use Illuminate\Validation\ValidationException;

final class RejectJobApplicationAction
{
    public function execute(JobApplication $application): JobApplication
    {
        if ($application->status !== JobApplicationStatus::PENDING) {
            throw ValidationException::withMessages([
                'status' => 'Seules les candidatures en attente peuvent être refusées.',
            ]);
        }

        $application->update(['status' => JobApplicationStatus::REJECTED]);

        return $application->fresh(['jobOffer.company', 'user']);
    }
}
