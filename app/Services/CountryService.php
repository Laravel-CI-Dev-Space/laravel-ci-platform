<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CountryService
{
    private const CACHE_KEY = 'countries_list';
    private const CACHE_TTL = 24 * 60 * 60; // 24 hours in seconds

    /**
     * Fetches the country list from RestCountries API, cached for 24 hours.
     * Cache::lock prevents the thundering herd problem: if many requests hit
     * simultaneously on a cache miss, only one calls the external API.
     */
    public function getCountries(): array
    {
        if ($cached = Cache::get(self::CACHE_KEY)) {
            return $cached;
        }

        $lock = Cache::lock('countries_rebuild', 10);

        try {
            if ($lock->get()) {
                // Re-check after acquiring the lock - another request may have populated it
                if ($cached = Cache::get(self::CACHE_KEY)) {
                    return $cached;
                }

                $countries = $this->fetchFromApi();
                Cache::put(self::CACHE_KEY, $countries, self::CACHE_TTL);

                return $countries;
            }
        } finally {
            $lock->release();
        }

        // Could not acquire lock within timeout - return fallback without blocking
        return $this->fallback();
    }

    private function fetchFromApi(): array
    {
        $response = Http::get('https://restcountries.com/v3.1/all', [
            'fields' => 'name',
        ]);

        if ($response->failed()) {
            return $this->fallback();
        }

        return collect($response->json())
            ->map(fn ($c) => $c['name']['common'] ?? null)
            ->filter()
            ->sort()
            ->values()
            ->mapWithKeys(fn ($name) => [$name => $name])
            ->toArray();
    }

    /** Fallback list of West/Central African countries used when the API is unavailable. */
    private function fallback(): array
    {
        $countries = [
            "Bénin", "Burkina Faso", "Cameroun", "Côte d'Ivoire",
            "France", "Gabon", "Ghana", "Guinée", "Mali", "Maroc",
            "Mauritanie", "Niger", "Nigeria", "RD Congo",
            "Sénégal", "Togo", "Tunisie",
        ];

        return array_combine($countries, $countries);
    }
}
