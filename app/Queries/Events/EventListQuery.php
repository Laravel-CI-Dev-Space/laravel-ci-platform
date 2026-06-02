<?php

declare(strict_types=1);

namespace App\Queries\Events;

use App\Enums\Events\EventRegistrationStatus;
use App\Models\Event;
use Illuminate\Database\Eloquent\Builder;

final class EventListQuery
{
    /**
     * @return Builder<Event>
     */
    public static function make(string $period, ?string $typeSlug): Builder
    {
        $query = Event::query()
            ->with(['type'])
            ->withCount([
                'registrations as confirmed_registrations_count' => fn ($q) => $q->where(
                    'status',
                    EventRegistrationStatus::CONFIRMED,
                ),
            ]);

        $query = match ($period) {
            'past'  => $query->past(),
            'all'   => $query->published(),
            default => $query->upcoming(),
        };

        if ($typeSlug !== null && $typeSlug !== '') {
            $query->ofType($typeSlug);
        }

        return $query->orderBy('start_date');
    }
}
