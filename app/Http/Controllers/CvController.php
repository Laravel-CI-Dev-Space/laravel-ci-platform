<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CvController extends Controller
{
    /**
     * Sert le CV d'un utilisateur de façon sécurisée.
     * Seul le propriétaire et les admins peuvent télécharger.
     */
    public function download(int $userId): StreamedResponse|RedirectResponse
    {
        /** @var User $viewer */
        $viewer = auth()->user();

        abort_unless(
            $viewer->id === $userId || $viewer->hasAnyRole(['admin', 'super-admin']),
            403,
            'Accès non autorisé.'
        );

        $profile = Profile::where('user_id', $userId)->firstOrFail();

        abort_unless($profile->cv, 404);
        abort_unless(Storage::disk('local')->exists("cv/{$profile->cv}"), 404);

        return Storage::disk('local')->download("cv/{$profile->cv}");
    }
}
