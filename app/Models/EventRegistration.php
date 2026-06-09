<?php

namespace App\Models;

use App\Enums\Events\EventRegistrationStatus;
use App\Enums\Events\EventReminderType;
use Database\Factories\EventRegistrationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'event_id',
    'user_id',
    'status',
    'reminder_types',
])]
class EventRegistration extends Model
{
    /** @use HasFactory<EventRegistrationFactory> */
    use HasFactory;

    // Registrations are immutable after creation except status/reminder_types updates.
    public const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'status'         => EventRegistrationStatus::class,
            'reminder_types' => 'array',
        ];
    }

    /**
     * Whitelist reminder type values from user input.
     *
     * @param  list<string>  $types  Raw values from checkboxes, e.g. ['J-7', 'H-1']
     * @return list<string> Valid EventReminderType values only
     */
    public static function sanitizeReminderTypes(array $types): array
    {
        $valid = array_column(EventReminderType::cases(), 'value');

        return array_values(array_unique(array_intersect($types, $valid)));
    }

    /** Whether the member opted in to at least one reminder slot. */
    public function wantsReminders(): bool
    {
        return count($this->reminder_types ?? []) > 0;
    }

    public function hasReminderType(EventReminderType $type): bool
    {
        return in_array($type->value, $this->reminder_types ?? [], true);
    }

    /** Human-readable labels for the dashboard (French UI strings). */
    public function reminderTypesLabel(): string
    {
        $types = $this->reminder_types ?? [];

        if ($types === []) {
            return 'Aucun rappel';
        }

        return collect($types)
            ->map(fn (string $value) => EventReminderType::from($value)->label())
            ->implode(', ');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
