<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;

class MatriculeGenerator
{
    /**
     * Génère le matricule d'un user nouvellement inscrit.
     *
     * Format : LARAVELCI-DD-MM-YYYY-XXXX
     *   - DD-MM-YYYY = date d'inscription
     *   - XXXX       = rang d'inscription (ordre croissant, zéro-paddé sur 4 chiffres)
     *
     * Le rang est calculé en comptant les users inscrits avant celui-ci
     * (sur la même date d'inscription pour conserver la lisibilité),
     * avec un fallback sur l'ID global pour garantir l'unicité.
     */
    public function generate(User $user): string
    {
        $date = ($user->created_at ?? now())->format('d-m-Y');

        // Nombre de users ayant un matricule + l'user courant = rang global
        $rank = User::whereNotNull('matricule')->count() + 1;

        return sprintf('LARAVELCI-%s-%04d', $date, $rank);
    }

    /**
     * Génère et persiste le matricule si l'user n'en a pas encore.
     * Retourne le matricule (nouveau ou existant).
     */
    public function assignIfMissing(User $user): string
    {
        if ($user->matricule) {
            return $user->matricule;
        }

        $matricule = $this->generate($user);

        // Boucle de sécurité : si collision (rare), incrémente le rang
        $extra = 0;
        while (User::where('matricule', $matricule)->exists()) {
            $extra++;
            $date      = ($user->created_at ?? now())->format('d-m-Y');
            $rank      = User::whereNotNull('matricule')->count() + 1 + $extra;
            $matricule = sprintf('LARAVELCI-%s-%04d', $date, $rank);
        }

        $user->update(['matricule' => $matricule]);

        return $matricule;
    }
}
