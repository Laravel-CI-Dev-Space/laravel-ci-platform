<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use Illuminate\Contracts\View\View;

class MemberJobApplicationController extends Controller
{
    public function show(JobApplication $jobApplication): View
    {
        $this->authorize('view', $jobApplication);

        $jobApplication->load(['jobOffer.company', 'user']);

        return view('dashboard.member.application-show', [
            'application' => $jobApplication,
        ]);
    }
}
