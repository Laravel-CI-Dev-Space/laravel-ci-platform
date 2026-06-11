<?php

declare(strict_types=1);

namespace App\Actions\Jobs;

use App\Enums\Jobs\JobOfferStatus;
use App\Models\JobAlert;
use App\Models\JobOffer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

final class MatchJobAlertsForOfferAction
{
    /**
     * Active alerts whose criteria match the given offer.
     *
     * @return Collection<int, JobAlert>
     */
    public function execute(JobOffer $offer): Collection
    {
        if ($offer->status !== JobOfferStatus::ACTIVE) {
            return new Collection;
        }

        $offer->loadMissing(['company', 'skills']);

        return JobAlert::query()
            ->with('user')
            ->where('is_active', true)
            ->whereHas('user', fn ($query) => $query->where('is_active', true))
            ->get()
            ->filter(fn (JobAlert $alert) => $this->matches($alert, $offer));
    }

    public function matches(JobAlert $alert, JobOffer $offer): bool
    {
        if ($offer->status !== JobOfferStatus::ACTIVE) {
            return false;
        }

        if (filled($alert->keywords) && ! $this->matchesKeywords($alert->keywords, $offer)) {
            return false;
        }

        if (filled($alert->location) && ! $this->matchesLocation($alert->location, $offer->location)) {
            return false;
        }

        if ($alert->type !== null && $alert->type !== $offer->type) {
            return false;
        }

        return filled($alert->keywords) || filled($alert->location) || $alert->type !== null;
    }

    private function matchesKeywords(string $keywords, JobOffer $offer): bool
    {
        $terms = $this->parseKeywordTerms($keywords);

        if ($terms === []) {
            return false;
        }

        $haystack = $this->buildSearchHaystack($offer);

        foreach ($terms as $term) {
            if (! str_contains($haystack, $this->normalizeText($term))) {
                return false;
            }
        }

        return true;
    }

    private function matchesLocation(string $alertLocation, ?string $offerLocation): bool
    {
        if ($offerLocation === null || $offerLocation === '') {
            return false;
        }

        return str_contains(
            $this->normalizeText($offerLocation),
            $this->normalizeText(trim($alertLocation)),
        );
    }

    /**
     * @return list<string>
     */
    private function parseKeywordTerms(string $keywords): array
    {
        $terms = preg_split('/[\s,;]+/u', trim($keywords)) ?: [];

        return array_values(array_filter(
            array_map(
                fn (string $term) => trim($term, " \t\n\r\0\x0B,;."),
                $terms,
            ),
            fn (string $term) => $term !== '',
        ));
    }

    private function buildSearchHaystack(JobOffer $offer): string
    {
        $parts = [
            $offer->title,
            $offer->description,
            $offer->location,
            $offer->type?->label(),
            $offer->type?->value,
            $offer->company?->name,
            $offer->company?->description,
            ...$offer->skills->pluck('name')->all(),
        ];

        return $this->normalizeText(collect($parts)->filter()->implode(' '));
    }

    private function normalizeText(string $value): string
    {
        return Str::lower(Str::ascii($value));
    }
}
