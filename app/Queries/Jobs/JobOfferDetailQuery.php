<?php

declare(strict_types=1);

namespace App\Queries\Jobs;

use App\Models\JobOffer;
use Illuminate\Database\Eloquent\Builder;

final class JobOfferDetailQuery
{
    /**
     * @return Builder<JobOffer>
     */
    public static function make(?int $userId = null): Builder
    {
        $query = JobOffer::query()
            ->with(['company', 'category', 'skills']);

        if ($userId !== null) {
            $query->with([
                'applications' => fn ($q) => $q->where('user_id', $userId),
            ]);
        }

        return $query;
    }

    public static function findById(int $id, ?int $userId = null): JobOffer
    {
        return self::make($userId)->whereKey($id)->firstOrFail();
    }
}
