<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\NewsletterSubscriber;
use Illuminate\Console\Command;

class NewsletterImportEventRegistrants extends Command
{
    protected $signature = 'newsletter:import-event-registrants
                            {event : ID ou slug de l\'événement}
                            {--dry-run : Affiche le résultat sans insérer}';

    protected $description = 'Ajoute les inscrits confirmés d\'un événement à la newsletter';

    public function handle(): int
    {
        $identifier = $this->argument('event');
        $dryRun     = $this->option('dry-run');

        $event = is_numeric($identifier)
            ? Event::find($identifier)
            : Event::where('slug', $identifier)->first();

        if (! $event) {
            $this->error("Événement introuvable : {$identifier}");
            return self::FAILURE;
        }

        $this->info("Événement : {$event->title}");

        // Membres confirmés
        $members = $event->confirmedRegistrations()
            ->with('user:id,name,email')
            ->get()
            ->map(fn ($r) => [
                'email' => $r->user->email,
                'name'  => $r->user->name,
            ]);

        // Invités confirmés
        $guests = $event->confirmedGuestRegistrations()
            ->get()
            ->map(fn ($r) => [
                'email' => $r->email,
                'name'  => trim("{$r->first_name} {$r->last_name}"),
            ]);

        $all = $members->merge($guests)
            ->filter(fn ($r) => filled($r['email']))
            ->unique('email')
            ->values();

        $this->info("Inscrits confirmés trouvés : {$all->count()} (membres : {$members->count()}, invités : {$guests->count()})");

        if ($dryRun) {
            $this->table(['Email', 'Nom'], $all->map(fn ($r) => [$r['email'], $r['name']])->toArray());
            $this->warn('Mode dry-run : aucune insertion.');
            return self::SUCCESS;
        }

        $added   = 0;
        $already = 0;

        foreach ($all as $row) {
            $exists = NewsletterSubscriber::where('email', $row['email'])->first();

            if ($exists) {
                // Réactiver si désabonné
                if ($exists->unsubscribed_at !== null) {
                    $exists->update(['unsubscribed_at' => null]);
                    $added++;
                } else {
                    $already++;
                }
                continue;
            }

            NewsletterSubscriber::create([
                'email' => $row['email'],
                'name'  => $row['name'],
            ]);
            $added++;
        }

        $this->info("Ajoutés / réactivés : {$added}");
        $this->line("Déjà abonnés        : {$already}");

        return self::SUCCESS;
    }
}
