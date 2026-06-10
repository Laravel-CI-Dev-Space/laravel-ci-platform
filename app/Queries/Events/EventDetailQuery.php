<?php

declare(strict_types=1);

namespace App\Queries\Events;

use App\Enums\Events\EventRegistrationStatus;
use App\Models\Event;
use Illuminate\Database\Eloquent\Builder;

final class EventDetailQuery
{
    /**
     * @return Builder<Event>
     */
    public static function make(?int $userId = null): Builder
    {
        $query = Event::query()
            ->with(['type', 'speakers'])
            ->withCount([
                'registrations as confirmed_registrations_count' => fn ($q) => $q->where(
                    'status',
                    EventRegistrationStatus::CONFIRMED,
                ),
            ]);

        if ($userId !== null) {
            $query->with([
                'registrations' => fn ($q) => $q->where('user_id', $userId),
                'waitlists'     => fn ($q) => $q->where('user_id', $userId),
            ]);
        }

        return $query;
    }

    public static function findBySlug(string $slug, ?int $userId = null): Event
    {
        return self::make($userId)->where('slug', $slug)->firstOrFail();
    }

    public static function findById(int $id, ?int $userId = null): Event
    {
        return self::make($userId)->whereKey($id)->firstOrFail();
    }
}
