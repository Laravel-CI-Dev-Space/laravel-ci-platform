<?php

declare(strict_types=1);

namespace App\Queries\Jobs;

use App\Enums\Jobs\JobOfferType;
use App\Models\JobOffer;
use Illuminate\Database\Eloquent\Builder;

final class JobOfferListQuery
{
    /**
     * @return Builder<JobOffer>
     */
    public static function make(
        ?string $type,
        bool $remoteOnly,
        ?string $skillSlug,
        ?string $categorySlug,
        string $sort,
    ): Builder {
        $query = JobOffer::query()
            ->with(['company', 'category', 'skills'])
            ->active();

        if ($type !== null && $type !== '' && JobOfferType::tryFrom($type)) {
            $query->where('type', $type);
        }

        if ($remoteOnly) {
            $query->where('type', JobOfferType::REMOTE);
        }

        if ($skillSlug !== null && $skillSlug !== '') {
            $query->whereHas('skills', fn (Builder $q) => $q->where('slug', $skillSlug));
        }

        if ($categorySlug !== null && $categorySlug !== '') {
            $query->whereHas('category', fn (Builder $q) => $q->where('slug', $categorySlug));
        }

        return match ($sort) {
            'title' => $query->orderBy('title'),
            default => $query->orderByDesc('created_at'),
        };
    }
}
