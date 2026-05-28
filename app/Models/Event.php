<?php

namespace App\Models;

use App\Models\EventRegistration;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'created_by', 'title', 'slug', 'description', 'program', 'type',
    'location', 'online_url', 'cover_image', 'starts_at', 'ends_at',
    'capacity', 'registrations_count', 'waitlist_enabled', 'status',
    'cancellation_reason', 'reminder_7d_sent', 'reminder_1d_sent',
])]
class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'starts_at'           => 'datetime',
            'ends_at'             => 'datetime',
            'waitlist_enabled'    => 'boolean',
            'reminder_7d_sent'    => 'boolean',
            'reminder_1d_sent'    => 'boolean',
            'capacity'            => 'integer',
            'registrations_count' => 'integer',
        ];
    }

    public function creator(): BelongsTo        { return $this->belongsTo(User::class, 'created_by'); }
    public function registrations(): HasMany     { return $this->hasMany(EventRegistration::class); }
    public function confirmedRegistrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class)->where('status', 'confirmed');
    }

    public function scopePublished($query)  { return $query->where('status', 'published'); }
    public function scopeUpcoming($query)   { return $query->where('starts_at', '>', now()); }
    public function scopePast($query)       { return $query->where('starts_at', '<=', now()); }
    public function scopeByType($query, string $type) { return $query->where('type', $type); }

    public function isFull(): bool          { return $this->capacity !== null && $this->registrations_count >= $this->capacity; }
    public function isUpcoming(): bool      { return $this->starts_at->isFuture(); }
    public function isPast(): bool          { return $this->ends_at->isPast(); }
    public function isOnline(): bool        { return $this->online_url !== null; }
    public function spotsLeft(): ?int       { return $this->capacity ? $this->capacity - $this->registrations_count : null; }
}
