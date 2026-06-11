<?php

declare(strict_types=1);

namespace App\Jobs\Notifications;

use App\Enums\UserRole;
use App\Models\JobOffer;
use App\Models\SiteSetting;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class SendJobAlerts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Correspondance entre le niveau Laravel d'un membre et les niveaux d'offres pertinents.
     */
    private const LEVEL_MAP = [
        'debutant'      => ['junior'],
        'intermediaire' => ['junior', 'intermediate'],
        'avance'        => ['intermediate', 'senior'],
        'expert'        => ['senior', 'lead'],
        'maitre'        => ['senior', 'lead'],
    ];

    /**
     * Envoie une alerte emploi aux membres actifs dont le profil correspond à
     * de nouvelles offres d'emploi actives. La fréquence (quotidienne,
     * hebdomadaire ou mensuelle) est configurable par les administrateurs
     * via les paramètres du site (clé `job_alert_frequency`).
     */
    public function handle(NotificationService $notificationService): void
    {
        $frequency = SiteSetting::get('job_alert_frequency', 'weekly');

        if (! $this->isDueToday($frequency)) {
            return;
        }

        $members = User::role(UserRole::Member->value)
            ->whereHas('profile')
            ->with('profile')
            ->get();

        $recentOffers = JobOffer::active()
            ->notExpired()
            ->where('published_at', '>=', $this->periodStart($frequency))
            ->with(['company', 'skills'])
            ->get();

        if ($recentOffers->isEmpty()) {
            return;
        }

        foreach ($members as $member) {
            $matches = $this->matchingOffers($member, $recentOffers);

            if ($matches->isNotEmpty()) {
                $notificationService->sendJobAlert($member, $matches);
            }
        }
    }

    /**
     * Détermine si le job doit s'exécuter aujourd'hui selon la fréquence configurée.
     */
    private function isDueToday(string $frequency): bool
    {
        return match ($frequency) {
            'daily'   => true,
            'monthly' => now()->isSameDay(now()->startOfMonth()),
            default   => now()->isMonday(), // weekly
        };
    }

    /**
     * Calcule la date de début de la période couverte par l'alerte.
     */
    private function periodStart(string $frequency): Carbon
    {
        return match ($frequency) {
            'daily'   => now()->subDay(),
            'monthly' => now()->subMonth(),
            default   => now()->subWeek(), // weekly
        };
    }

    /**
     * Filtre les offres correspondant au niveau Laravel et/ou à la stack technique du membre.
     */
    private function matchingOffers(User $member, Collection $offers): Collection
    {
        $levels = self::LEVEL_MAP[$member->profile->laravel_level ?? ''] ?? [];
        $stack  = collect($member->profile->tech_stack ?? [])
            ->map(fn (string $skill) => mb_strtolower($skill))
            ->all();

        return $offers->filter(function (JobOffer $offer) use ($levels, $stack) {
            if ($offer->level === 'any' || in_array($offer->level, $levels, true)) {
                return true;
            }

            return $offer->skills->contains(
                fn ($skill) => in_array(mb_strtolower($skill->name), $stack, true)
            );
        })->values();
    }
}
