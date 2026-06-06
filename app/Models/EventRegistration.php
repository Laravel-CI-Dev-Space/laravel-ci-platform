<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'event_id', 'user_id', 'status', 'reminder_sent',
    'ical_token', 'registered_at', 'cancelled_at', 'cancellation_reason',
    'amount_paid', 'promo_code_used', 'discount_applied', 'payment_status',
    'ticket_number', 'ticket_qr_token',
])]
class EventRegistration extends Model
{
    protected function casts(): array
    {
        return [
            'reminder_sent'    => 'boolean',
            'registered_at'    => 'datetime',
            'cancelled_at'     => 'datetime',
            'amount_paid'      => 'decimal:2',
            'discount_applied' => 'decimal:2',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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

    public function isAttended(): bool
    {
        return $this->status === 'attended';
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isFreeRegistration(): bool
    {
        return $this->payment_status === 'free';
    }

    public function canCancel(): bool
    {
        return $this->isConfirmed() && $this->event->starts_at->isAfter(now()->addDays(2));
    }
}
