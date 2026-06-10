<?php

namespace App\Policies;

use App\Enums\Events\EventStatus;
use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Event $event): bool
    {
        if ($event->status === EventStatus::PUBLISHED) {
            return true;
        }

        return $user !== null && $user->hasAnyRole(['super-admin', 'admin', 'moderateur']);
    }

    public function register(User $user, Event $event): bool
    {
        if (! $user->hasRole('member')) {
            return false;
        }

        if (! $event->isRegisterable()) {
            return false;
        }

        if ($event->registrationFor($user) !== null || $event->waitlistEntryFor($user) !== null) {
            return false;
        }

        return true;
    }
}
