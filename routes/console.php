<?php

use App\Actions\Jobs\ExpireOldOffers;
use App\Jobs\Events\MarkCompletedEvents;
use App\Jobs\Events\SendEventReminder;
use App\Jobs\Notifications\SendJobAlerts;
use App\Jobs\PlatformDailyStatsJob;
use App\Models\Event;
use App\Models\SiteSetting;
use App\Services\Jobs\JobOfferService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
 * ── Traitement de la file d'attente (QUEUE_CONNECTION=database) ──
 * Sur un hébergement mutualisé sans worker `queue:work` permanent,
 * RunScheduler déclenche `schedule:run` à chaque requête : ce job
 * traite donc les jobs en attente (notifications, emails) par lots
 * d'au plus 50, pendant 50s max, sans bloquer indéfiniment.
 */
Schedule::command('queue:work --stop-when-empty --max-time=50 --max-jobs=50 --tries=3')
    ->everyMinute()
    ->name('process-queue')
    ->withoutOverlapping();

/* ── Expire les offres d'emploi périmées (quotidien, minuit) ── */
Schedule::call(fn () => app(ExpireOldOffers::class)->handle(app(JobOfferService::class)))
    ->daily()
    ->name('expire-old-job-offers')
    ->withoutOverlapping();

/* ── Rappels J-7 avant les événements ── */
Schedule::call(function (): void {
    $events = Event::published()
        ->upcoming()
        ->where('reminder_7d_sent', false)
        ->where('starts_at', '<=', now()->addDays(7))
        ->where('starts_at', '>', now()->addDays(6))
        ->get();

    foreach ($events as $event) {
        SendEventReminder::dispatch($event, '7d');
    }
})->daily()->name('send-event-reminders-7d')->withoutOverlapping();

/* ── Rappels J-1 avant les événements ── */
Schedule::call(function (): void {
    $events = Event::published()
        ->upcoming()
        ->where('reminder_1d_sent', false)
        ->where('starts_at', '<=', now()->addDays(1))
        ->where('starts_at', '>', now())
        ->get();

    foreach ($events as $event) {
        SendEventReminder::dispatch($event, '1d');
    }
})->daily()->name('send-event-reminders-1d')->withoutOverlapping();

/* ── Marque les événements passés comme terminés ── */
Schedule::job(new MarkCompletedEvents)->daily()->name('mark-completed-events')->withoutOverlapping();

/* ── Alertes emploi (fréquence configurable par les admins) ── */
Schedule::job(new SendJobAlerts)->daily()->name('send-job-alerts')->withoutOverlapping();

/* ── Stats plateforme pré-calculées (toutes les heures) ── */
Schedule::job(new PlatformDailyStatsJob)->hourly()->name('platform-daily-stats')->withoutOverlapping();

/* ── Recalcul des points et grades de réputation (fréquence configurable) ── */
$reputationSchedule = Schedule::command('reputation:recalculate')->name('recalculate-reputations')->withoutOverlapping();

try {
    $reputationFrequency = SiteSetting::get('reputation_recalculate_frequency', 'monthly');
} catch (Throwable) {
    $reputationFrequency = 'monthly';
}

match ($reputationFrequency) {
    'daily'  => $reputationSchedule->daily(),
    'weekly' => $reputationSchedule->weekly(),
    default  => $reputationSchedule->monthly(),
};
