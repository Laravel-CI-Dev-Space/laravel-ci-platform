<?php

declare(strict_types=1);

namespace App\Livewire\Dashboard;

use App\Livewire\Concerns\ManagesEventReminderPreferences;
use App\Models\EventRegistration;
use App\Models\User;
use App\Services\Events\EventService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Dashboard event card with cover image, icon actions, and reminder modal.
 *
 * @property 'card'|'row' $variant Full card on /events page, compact row on overview.
 */
class RegisteredEventCard extends Component
{
    use AuthorizesRequests;
    use ManagesEventReminderPreferences;

    #[Locked]
    public int $registrationId;

    /** @var 'card'|'row' */
    public string $variant = 'card';

    public function mount(int $registrationId, string $variant = 'card'): void
    {
        $this->registrationId    = $registrationId;
        $this->variant           = $variant;
        $this->selectedReminders = $this->currentReminderTypes();
    }

    public function saveReminderPreferences(EventService $eventService): void
    {
        $registration = $this->registration();
        $event        = $registration->event;
        $user         = auth()->user();

        if (! $user instanceof User || $event === null) {
            return;
        }

        $this->authorize('manageReminders', $event);

        try {
            $this->selectedReminders = $eventService->updateReminderPreferences(
                $event,
                $user,
                $this->selectedReminders,
            );

            $this->showReminderModal = false;

            $this->dispatch('dash-toast', message: $this->reminderSaveSuccessMessage());
        } catch (ValidationException $e) {
            $this->dispatch('dash-toast', message: $e->validator->errors()->first() ?? 'Action impossible.', type: 'error');
        }
    }

    protected function currentReminderTypes(): array
    {
        return $this->registration()->reminder_types ?? [];
    }

    private function registration(): EventRegistration
    {
        return EventRegistration::query()
            ->with('event.type')
            ->whereKey($this->registrationId)
            ->where('user_id', auth()->id())
            ->firstOrFail();
    }

    public function render(): View
    {
        $registration = $this->registration();
        $event        = $registration->event;

        return view('livewire.dashboard.registered-event-card', [
            'registration' => $registration,
            'event'        => $event,
        ]);
    }
}
