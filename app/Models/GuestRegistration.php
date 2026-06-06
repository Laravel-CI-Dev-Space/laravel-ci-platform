<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'event_id', 'first_name', 'last_name', 'email', 'whatsapp', 'photo',
    'status', 'amount_paid', 'promo_code_used', 'discount_applied', 'payment_status',
    'ticket_number', 'ticket_qr_token', 'registered_at', 'cancelled_at', 'cancellation_reason',
])]
class GuestRegistration extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
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

    public function fullName(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
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

    public function hasTicket(): bool
    {
        return $this->ticket_number !== null;
    }

    public function canCancel(): bool
    {
        return $this->isConfirmed()
            && $this->event->starts_at->diffInDays(now()) >= 2;
    }
}
