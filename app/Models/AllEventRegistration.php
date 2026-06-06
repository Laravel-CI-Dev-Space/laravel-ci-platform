<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AllEventRegistration extends Model
{
    protected $table = 'all_event_registrations';

    /** View is read-only — no inserts/updates allowed. */
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'amount_paid'      => 'decimal:2',
            'discount_applied' => 'decimal:2',
            'registered_at'    => 'datetime',
            'created_at'       => 'datetime',
            'updated_at'       => 'datetime',
        ];
    }

    /** @return BelongsTo<Event, self> */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function isMember(): bool
    {
        return $this->participant_type === 'member';
    }

    public function isGuest(): bool
    {
        return $this->participant_type === 'guest';
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    public function isWaitlisted(): bool
    {
        return $this->status === 'waitlisted';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }
}
