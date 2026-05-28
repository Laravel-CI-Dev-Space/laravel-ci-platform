<?php

namespace App\Models;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'event_id', 'user_id', 'status', 'reminder_sent',
    'ical_token', 'registered_at', 'cancelled_at', 'cancellation_reason',
])]
class EventRegistration extends Model
{
    protected function casts(): array
    {
        return [
            'reminder_sent' => 'boolean',
            'registered_at' => 'datetime',
            'cancelled_at'  => 'datetime',
        ];
    }

    public function event(): BelongsTo { return $this->belongsTo(Event::class); }
    public function user(): BelongsTo  { return $this->belongsTo(User::class); }

    public function isConfirmed(): bool  { return $this->status === 'confirmed'; }
    public function isWaitlisted(): bool { return $this->status === 'waitlisted'; }
    public function isCancelled(): bool  { return $this->status === 'cancelled'; }
    public function isAttended(): bool   { return $this->status === 'attended'; }

    public function canCancel(): bool
    {
        return $this->isConfirmed() && $this->event->starts_at->diffInDays(now()) >= 2;
    }
}
