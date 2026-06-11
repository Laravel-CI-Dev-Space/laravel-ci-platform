<?php

declare(strict_types=1);

namespace App\Services\Jobs;

use App\Actions\Jobs\MatchJobAlertsForOfferAction;
use App\Enums\Jobs\JobOfferType;
use App\Models\JobAlert;
use App\Models\JobOffer;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

final class JobAlertService
{
    public function __construct(
        private readonly MatchJobAlertsForOfferAction $matchJobAlerts,
        private readonly NotificationService $notifications,
    ) {}

    /**
     * Create a job alert.
     *
     * @param  array{keywords?: string|null, location?: string|null, type?: string|null}  $data
     */
    public function create(User $user, array $data): JobAlert
    {
        $this->assertHasCriteria($data);

        return JobAlert::create([
            'user_id'  => $user->id,
            'keywords' => $this->normalizeNullableString($data['keywords'] ?? null),
            'location' => $this->normalizeNullableString($data['location'] ?? null),
            'type'     => isset($data['type']) && $data['type'] !== '' && $data['type'] !== null
                ? JobOfferType::from($data['type'])
                : null,
            'is_active' => true,
        ]);
    }

    /**
     * @param  array{keywords?: string|null, location?: string|null, type?: string|null}  $data
     */
    public function update(JobAlert $alert, array $data): JobAlert
    {
        $this->assertHasCriteria($data);

        $alert->update([
            'keywords' => $this->normalizeNullableString($data['keywords'] ?? null),
            'location' => $this->normalizeNullableString($data['location'] ?? null),
            'type'     => isset($data['type']) && $data['type'] !== '' && $data['type'] !== null
                ? JobOfferType::from($data['type'])
                : null,
        ]);

        return $alert->fresh();
    }

    /**
     * Activate a job alert.
     */
    public function activate(JobAlert $alert): JobAlert
    {
        $alert->update(['is_active' => true]);

        return $alert->fresh();
    }

    /**
     * Deactivate a job alert.
     */
    public function deactivate(JobAlert $alert): JobAlert
    {
        $alert->update(['is_active' => false]);

        return $alert->fresh();
    }

    /**
     * Delete a job alert.
     */
    public function delete(JobAlert $alert): void
    {
        $alert->delete();
    }

    /**
     * Notify users with matching alerts about a new job offer.
     */
    public function notifyMatchingAlerts(JobOffer $offer): void
    {
        $offer->loadMissing('company');

        /** @var Collection<int, JobAlert> $alerts */
        $alerts = $this->matchJobAlerts->execute($offer);

        $alerts
            ->unique('user_id')
            ->each(function (JobAlert $alert) use ($offer): void {
                $this->notifications->sendJobAlert($alert->user, $offer);
            });
    }

    /**
     * @param  array{keywords?: string|null, location?: string|null, type?: string|null}  $data
     */
    private function assertHasCriteria(array $data): void
    {
        $hasKeywords = filled($this->normalizeNullableString($data['keywords'] ?? null));
        $hasLocation = filled($this->normalizeNullableString($data['location'] ?? null));
        $hasType     = isset($data['type']) && $data['type'] !== '' && $data['type'] !== null;

        if (! $hasKeywords && ! $hasLocation && ! $hasType) {
            throw ValidationException::withMessages([
                'criteria' => 'Renseignez au moins un critère : mots-clés, lieu ou type de contrat.',
            ]);
        }
    }

    /**
     * Normalize a nullable string.
     */
    private function normalizeNullableString(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);

        return $value !== '' ? $value : null;
    }
}
