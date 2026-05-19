<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CountryService
{
    private const CACHE_KEY = 'countries_list';
    private const CACHE_TTL = 24 * 60 * 60; // 24h en secondes

    /**
     * Récupère la liste des pays depuis RestCountries.
     * Cache::lock évite le thundering herd : si plusieurs requêtes arrivent
     * simultanément lors d'une expiration, une seule appelle l'API externe.
     */
    public function getCountries(): array
    {
        if ($cached = Cache::get(self::CACHE_KEY)) {
            return $cached;
        }

        $lock = Cache::lock('countries_rebuild', 10);

        try {
            if ($lock->get()) {
                // Re-vérifier après acquisition du verrou (une autre requête a peut-être déjà peuplé le cache)
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

        // Verrou non obtenu dans le délai — retourner le fallback sans bloquer
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

    private function fallback(): array
    {
        $pays = [
            'Bénin', 'Burkina Faso', 'Cameroun', "Côte d'Ivoire",
            'France', 'Gabon', 'Ghana', 'Guinée', 'Mali', 'Maroc',
            'Mauritanie', 'Niger', 'Nigeria', 'RD Congo',
            'Sénégal', 'Togo', 'Tunisie',
        ];

        return array_combine($pays, $pays);
    }
}
