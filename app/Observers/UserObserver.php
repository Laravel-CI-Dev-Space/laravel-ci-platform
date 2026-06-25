<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\User;
use App\Services\MatriculeGenerator;

class UserObserver
{
    public function __construct(private MatriculeGenerator $matriculeGenerator) {}

    /** Génère le matricule dès qu'un user est créé. */
    public function created(User $user): void
    {
        $this->matriculeGenerator->assignIfMissing($user);
    }
}
