<?php

declare(strict_types=1);

namespace App\Services\Forum;

use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ReportService
{
    /**
     * Crée un signalement sur un contenu.
     * Vérifie que l'user n'a pas déjà signalé ce contenu.
     */
    public function createReport(User $user, Model $reportable, array $data): Report
    {
        $alreadyReported = Report::where([
            'reporter_id'     => $user->id,
            'reportable_id'   => $reportable->id,
            'reportable_type' => $reportable->getMorphClass(),
        ])->exists();

        abort_if($alreadyReported, 422, 'Vous avez déjà signalé ce contenu.');

        return Report::create([
            'reporter_id'     => $user->id,
            'reportable_id'   => $reportable->id,
            'reportable_type' => $reportable->getMorphClass(),
            'reason'          => $data['reason'],
            'description'     => $data['description'] ?? null,
            'status'          => 'pending',
        ]);
    }
}
