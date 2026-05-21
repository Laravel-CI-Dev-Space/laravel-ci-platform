<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CountryService
{
    /**
     * Récupère la liste des pays depuis RestCountries.
     * Mise en cache 24h.
     */
    public function getCountries(): array
    {
        return Cache::remember('countries_list', now()->addHours(24), function () {
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
        });
    }

    /**
     * Fallback si API indisponible.
     */
    private function fallback(): array
    {
        $pays = [
            "Bénin", "Burkina Faso", "Cameroun", "Côte d'Ivoire",
            "France", "Gabon", "Ghana", "Guinée", "Mali", "Maroc",
            "Mauritanie", "Niger", "Nigeria", "RD Congo",
            "Sénégal", "Togo", "Tunisie",
        ];
        return array_combine($pays, $pays);
    }
}
