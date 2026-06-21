<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\Analytics\AnalyticsService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackPageView
{
    public function __construct(private readonly AnalyticsService $analytics) {}

    public function handle(Request $request, Closure $next): Response
    {
        $start = microtime(true);

        $response = $next($request);

        if (! $request->ajax() && ! $request->expectsJson()) {
            $durationMs = (int) round((microtime(true) - $start) * 1000);
            $this->analytics->trackPageView($request, $response->getStatusCode(), $durationMs);
        }

        return $response;
    }
}
