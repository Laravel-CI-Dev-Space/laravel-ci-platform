<?php

declare(strict_types=1);

namespace App\Livewire\Concerns;

use App\Enums\Events\EventReminderType;

trait ManagesEventReminderPreferences
{
    /** @var list<string> */
    public array $selectedReminders = [];

    public bool $showReminderModal = false;

    public function openReminderModal(): void
    {
        $this->selectedReminders = $this->currentReminderTypes();
        $this->showReminderModal = true;
    }

    public function closeReminderModal(): void
    {
        $this->showReminderModal = false;
        $this->selectedReminders = $this->currentReminderTypes();
    }

    public function selectAllReminders(): void
    {
        $this->selectedReminders = array_column(EventReminderType::cases(), 'value');
    }

    public function clearAllReminders(): void
    {
        $this->selectedReminders = [];
    }

    /** @return list<string> */
    abstract protected function currentReminderTypes(): array;

    protected function reminderSaveSuccessMessage(): string
    {
        if ($this->selectedReminders === []) {
            return 'Aucun rappel email ne sera envoyé.';
        }

        $labels = collect($this->selectedReminders)
            ->map(fn (string $value) => EventReminderType::from($value)->label())
            ->implode(', ');

        return "Rappels enregistrés : {$labels}.";
    }
}
