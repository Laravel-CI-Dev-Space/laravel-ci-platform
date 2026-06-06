<?php

declare(strict_types=1);

namespace App\Services\Events;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class EventService
{
    /**
     * Récupère les événements publiés avec filtres et pagination.
     *
     * @param  string  $type  all / meetup / webinar / hackathon / conference / workshop
     * @param  string  $period  all / upcoming / past
     * @param  string  $sort  recent / soonest
     */
    public function getEvents(
        string $type = 'all',
        string $period = 'upcoming',
        string $sort = 'soonest',
        int $perPage = 9,
    ): LengthAwarePaginator {
        $query = Event::published()
            ->with(['creator'])
            ->withCount('registrations');

        if ($type !== 'all') {
            $query->where('type', $type);
        }

        $query->when($period === 'upcoming', fn (Builder $q) => $q->where('starts_at', '>', now()));
        $query->when($period === 'past', fn (Builder $q) => $q->where('starts_at', '<=', now()));

        return match ($sort) {
            'recent' => $query->orderByDesc('created_at')->paginate($perPage),
            default  => $query->orderBy('starts_at')->paginate($perPage),
        };
    }

    /**
     * Récupère un événement publié par son slug.
     * Eager loads : creator, registrations count.
     */
    public function getBySlug(string $slug): Event
    {
        return Event::published()
            ->with(['creator'])
            ->withCount(['confirmedRegistrations'])
            ->where('slug', $slug)
            ->firstOrFail();
    }

    /**
     * Récupère les événements à venir pour la page d'accueil.
     */
    public function getUpcomingEvents(int $limit = 3): Collection
    {
        return Event::published()
            ->upcoming()
            ->orderBy('starts_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Vérifie si un utilisateur est inscrit à un événement.
     */
    public function isRegistered(User $user, Event $event): bool
    {
        return EventRegistration::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->whereNotIn('status', ['cancelled'])
            ->exists();
    }

    /**
     * Récupère l'inscription d'un utilisateur à un événement.
     */
    public function getRegistration(User $user, Event $event): ?EventRegistration
    {
        return EventRegistration::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->whereNotIn('status', ['cancelled'])
            ->first();
    }

    /**
     * Vérifie si un événement est complet.
     */
    public function isFull(Event $event): bool
    {
        return $event->isFull();
    }

    /**
     * Retourne le nombre de places restantes (null si illimité).
     */
    public function spotsLeft(Event $event): ?int
    {
        return $event->spotsLeft();
    }
}
