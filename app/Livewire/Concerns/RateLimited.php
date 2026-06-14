<?php

declare(strict_types=1);

namespace App\Livewire\Concerns;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Limite la fréquence d'une action Livewire (par utilisateur connecté ou
 * par IP pour les invités). Les routes web sont throttlées, mais les
 * composants Livewire sont appelés via /livewire/update et contournent
 * ce throttling — d'où ce limiteur applicatif.
 */
trait RateLimited
{
    protected function tooManyAttempts(string $action, int $maxAttempts, int $decaySeconds = 60): bool
    {
        $key = "livewire:{$action}:" . (Auth::id() ?? request()->ip());

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            return true;
        }

        RateLimiter::hit($key, $decaySeconds);

        return false;
    }

    protected function rateLimitMessage(string $action): string
    {
        $key     = "livewire:{$action}:" . (Auth::id() ?? request()->ip());
        $seconds = RateLimiter::availableIn($key);

        return "Trop de tentatives. Veuillez réessayer dans {$seconds} secondes.";
    }
}
