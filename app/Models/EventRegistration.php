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

    public const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'status'         => EventRegistrationStatus::class,
            'reminder_types' => 'array',
        ];
    }

    /** @param list<string> $types */
    public static function sanitizeReminderTypes(array $types): array
    {
        $valid = array_column(EventReminderType::cases(), 'value');

        return array_values(array_unique(array_intersect($types, $valid)));
    }

    public function wantsReminders(): bool
    {
        return count($this->reminder_types ?? []) > 0;
    }

    public function hasReminderType(EventReminderType $type): bool
    {
        return in_array($type->value, $this->reminder_types ?? [], true);
    }

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
