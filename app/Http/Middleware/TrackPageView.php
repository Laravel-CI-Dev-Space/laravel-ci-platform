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
        $response = $next($request);

        if (
            $request->isMethod('GET')
            && ! $request->ajax()
            && ! $request->expectsJson()
            && $response->getStatusCode() < 400
        ) {
            $this->analytics->trackPageView($request);
        }

        return $response;
    }
}
