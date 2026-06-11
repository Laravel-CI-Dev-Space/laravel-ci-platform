<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * Déclenche le scheduler Laravel ("schedule:run") à partir des requêtes web,
 * pour les hébergements mutualisés où aucune tâche cron ne peut être configurée.
 *
 * Au plus une exécution par minute (verrou cache), lancée après l'envoi de la
 * réponse pour ne pas ralentir la navigation des visiteurs.
 */
class RunScheduler
{
    private const CACHE_KEY = 'scheduler:last-run-lock';

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (Cache::add(self::CACHE_KEY, true, 60)) {
            $this->runAfterResponse();
        }

        return $response;
    }

    private function runAfterResponse(): void
    {
        if (function_exists('fastcgi_finish_request')) {
            register_shutdown_function(function () {
                fastcgi_finish_request();
                Artisan::call('schedule:run');
            });

            return;
        }

        Artisan::call('schedule:run');
    }
}
