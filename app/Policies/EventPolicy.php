<?php

namespace App\Policies;

use App\Enums\Events\EventRegistrationStatus;
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

        return $user !== null && $user->hasAnyRole(['super-admin', 'admin', 'moderator']);
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

    public function cancelRegistration(User $user, Event $event): bool
    {
        if (! $user->hasRole('member') || ! $event->isUpcoming()) {
            return false;
        }

        $registration = $event->registrationFor($user);

        return $registration !== null
            && $registration->status === EventRegistrationStatus::CONFIRMED;
    }

    public function leaveWaitlist(User $user, Event $event): bool
    {
        return $user->hasRole('member')
            && $event->waitlistEntryFor($user) !== null;
    }

    public function downloadIcs(User $user, Event $event): bool
    {
        return $user->hasRole('member')
            && $event->registrationFor($user) !== null;
    }

    public function manageReminders(User $user, Event $event): bool
    {
        if (! $user->hasRole('member') || ! $event->isUpcoming()) {
            return false;
        }

        $registration = $event->registrationFor($user);

        return $registration !== null
            && $registration->status === EventRegistrationStatus::CONFIRMED;
    }
}
