<?php

declare(strict_types=1);

namespace App\Livewire\Dashboard;

use App\Enums\Jobs\JobOfferType;
use App\Models\JobAlert;
use App\Models\User;
use App\Services\Jobs\JobAlertService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class MemberJobAlerts extends Component
{
    use AuthorizesRequests;

    public string $keywords = '';

    public string $location = '';

    public ?string $type = null;

    public ?int $alertIdToDelete = null;

    /**
     * @return Collection<int, JobAlert>
     */
    #[Computed]
    public function alerts(): Collection
    {
        /** @var User $user */
        $user = auth()->user();

        return JobAlert::query()
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->get();
    }

    public function createAlert(JobAlertService $jobAlertService): void
    {
        $this->authorize('create', JobAlert::class);

        $validated = $this->validate([
            'keywords' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'type'     => ['nullable', Rule::enum(JobOfferType::class)],
        ]);

        /** @var User $user */
        $user = auth()->user();

        $jobAlertService->create($user, $validated);

        $this->reset(['keywords', 'location', 'type']);
        unset($this->alerts);

        $this->dispatch('app-toast', message: 'Alerte créée', type: 'success');
    }

    public function toggleActive(int $alertId, JobAlertService $jobAlertService): void
    {
        $alert = JobAlert::query()->findOrFail($alertId);

        $this->authorize('update', $alert);

        if ($alert->is_active) {
            $jobAlertService->deactivate($alert);
            $message = 'Alerte désactivée';
        } else {
            $jobAlertService->activate($alert);
            $message = 'Alerte activée';
        }

        unset($this->alerts);

        $this->dispatch('app-toast', message: $message, type: 'info');
    }

    public function openDeleteModal(int $alertId): void
    {
        $this->alertIdToDelete = $alertId;
    }

    public function closeDeleteModal(): void
    {
        $this->alertIdToDelete = null;
    }

    public function confirmDelete(JobAlertService $jobAlertService): void
    {
        if ($this->alertIdToDelete === null) {
            return;
        }

        $alert = JobAlert::query()->findOrFail($this->alertIdToDelete);

        $this->authorize('delete', $alert);

        $jobAlertService->delete($alert);

        $this->closeDeleteModal();
        unset($this->alerts);

        $this->dispatch('app-toast', message: 'Alerte supprimée', type: 'info');
    }

    #[Computed]
    public function alertToDelete(): ?JobAlert
    {
        if ($this->alertIdToDelete === null) {
            return null;
        }

        return JobAlert::query()->find($this->alertIdToDelete);
    }

    public function render(): View
    {
        return view('livewire.dashboard.member-job-alerts', [
            'contractTypes' => JobOfferType::cases(),
        ]);
    }
}
