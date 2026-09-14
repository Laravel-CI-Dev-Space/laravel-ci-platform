<?php

declare(strict_types=1);

namespace App\Console\Commands\Events;

use App\Models\Event;
use App\Models\GuestRegistration;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ImportLumaMeet01 extends Command
{
    protected $signature = 'events:import-luma-meet01
                            {--csv= : Chemin vers le fichier CSV (défaut : docs/luma-meet01.csv)}
                            {--dry-run : Simule l\'import sans écrire en base}
                            {--force : Recrée l\'événement s\'il existe déjà}';

    protected $description = 'Crée l\'événement Laravel CI Meet #01 et importe les inscrits Luma depuis le CSV exporté';

    // ── Données de l'événement ─────────────────────────────────────────────

    private const EVENT_SLUG = 'laravel-ci-meet-01';

    private array $eventData = [
        'title'       => 'Laravel CI Meet #01 — L\'IA au service des développeurs Laravel',
        'slug'        => self::EVENT_SLUG,
        'type'        => 'meetup',
        'status'      => 'completed',
        'location'    => 'Campus Epitech Côte d\'Ivoire, Centre Commercial K.A.V, Riviera Faya, Abidjan',
        'starts_at'   => '2026-09-12 09:30:00',
        'ends_at'     => '2026-09-12 16:30:00',
        'is_paid'     => false,
        'guest_registration_enabled' => true,
        'ticketing_enabled'          => false,
        'waitlist_enabled'           => false,
    ];

    private string $eventDescription = <<<'HTML'
<p>La communauté Laravel Côte d'Ivoire organise son tout premier événement officiel en présentiel sur le campus d'Epitech CI. Après plus d'un an d'activités techniques en ligne, cet événement rassemble les développeurs PHP/Laravel, Tech Leads, étudiants en informatique et passionnés d'IA autour de la thématique :</p>

<p><strong>« L'IA au service des développeurs Laravel — De l'assistance au développement à l'ingénierie augmentée : comment l'IA transforme le quotidien du développeur Laravel »</strong></p>

<h2>Intervenants &amp; Temps forts</h2>
<p>L'événement réunit un panel de 4 intervenants d'exception, experts du secteur tech, de l'intelligence artificielle et des workflows de développement augmenté.</p>

<ul>
<li><strong>Démonstration en direct :</strong> Présentation officielle et démo live du Hub communautaire open source développé par les membres de la communauté.</li>
<li><strong>Talks d'experts &amp; Panel :</strong> Débats autour de l'utilisation de l'IA générative dans le code, de l'autonomie des développeurs juniors et des bonnes pratiques de cybersécurité.</li>
<li><strong>Partner Spotlights :</strong> Interventions courtes de nos partenaires (Alal Finance et Bolli Africa) sur des cas d'usage réels d'IA et de digitalisation.</li>
</ul>

<h2>Lieu et Accès</h2>
<p>Lieu : Campus Epitech Côte d'Ivoire, Centre Commercial K.A.V, Riviera Faya, Abidjan<br>
Parking sécurisé disponible au sein du centre commercial. Accès facile pour les personnes à mobilité réduite.</p>
HTML;

    private string $eventProgram = <<<'HTML'
<h2>Programme complet</h2>
<ul>
<li><strong>09h30</strong> — Accueil et check-in (badges, café, orientation)</li>
<li><strong>10h00</strong> — Ouverture officielle · MC + mot de l'hôte Epitech Abidjan</li>
<li><strong>10h15</strong> — Le Hub de la communauté Laravel CI · Wilson Kouassi (Tech Lead, Excelliam)</li>
<li><strong>11h00</strong> — Du développeur assisté à l'équipe augmentée · Élisée Kambiré (Tech Lead, BLOK Technology)</li>
<li><strong>11h45</strong> — Designing AI Agents · Rygel Louv (Founder, Golmine AI)</li>
<li><strong>12h30</strong> — Partner spotlight — Bolli Africa</li>
<li><strong>12h40</strong> — Partner spotlight — ALAL Finance</li>
<li><strong>12h50</strong> — Pause repas &amp; réseautage</li>
<li><strong>14h00</strong> — De Laravel à l'agent IA · Doro Gueye (CTO, ALAL Finance)</li>
<li><strong>14h45</strong> — Votre API à un nouveau client : l'agent IA · Boubacar Ly (Tech Lead, Djoli)</li>
<li><strong>15h30</strong> — Panel — Agentic coding · Tous les speakers</li>
<li><strong>16h15</strong> — Photo de famille &amp; clôture</li>
</ul>
HTML;

    // ── Commande principale ────────────────────────────────────────────────

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');
        $csvPath  = $this->option('csv')
            ?? base_path('docs/luma-meet01-guests.csv');

        $this->info($isDryRun ? '🔍 Mode dry-run — aucune écriture en base' : '🚀 Import réel en base');
        $this->newLine();

        // ── 1. Créer ou retrouver l'événement ─────────────────────────────
        $event = $this->resolveEvent($isDryRun);

        // ── 2. Lire le CSV ─────────────────────────────────────────────────
        if (! file_exists($csvPath)) {
            $this->error("Fichier CSV introuvable : {$csvPath}");
            return self::FAILURE;
        }

        $rows = $this->parseCsv($csvPath);
        $this->info("📄 {$rows->count()} ligne(s) lues dans le CSV");
        $this->newLine();

        // ── 3. Importer ────────────────────────────────────────────────────
        $stats = [
            'member'    => 0, // email matche un User → event_registrations
            'guest'     => 0, // sinon → guest_registrations
            'skipped'   => 0, // déjà inscrit
            'errors'    => 0,
        ];

        $rows->each(function (array $row) use ($event, $isDryRun, &$stats): void {
            try {
                $result = $this->importRow($row, $event, $isDryRun);
                $stats[$result]++;
            } catch (\Throwable $e) {
                $stats['errors']++;
                $this->warn("  ⚠ {$row['email']} — {$e->getMessage()}");
            }
        });

        // ── 4. Mise à jour du compteur d'inscrits ─────────────────────────
        if (! $isDryRun && $event !== null) {
            $total = $stats['member'] + $stats['guest'];
            $event->update(['registrations_count' => $total + $event->confirmedGuestRegistrations()->count()]);
        }

        // ── 5. Résumé ──────────────────────────────────────────────────────
        $this->newLine();
        $this->table(
            ['Type', 'Nombre'],
            [
                ['Membres liés (event_registrations)', $stats['member']],
                ['Invités externes (guest_registrations)', $stats['guest']],
                ['Déjà inscrits (ignorés)', $stats['skipped']],
                ['Erreurs', $stats['errors']],
            ]
        );

        $this->newLine();
        $this->info($isDryRun ? '✅ Dry-run terminé — rien n\'a été écrit.' : '✅ Import terminé avec succès.');

        return self::SUCCESS;
    }

    // ── Création / résolution de l'événement ──────────────────────────────

    private function resolveEvent(bool $isDryRun): ?Event
    {
        $existing = Event::where('slug', self::EVENT_SLUG)->first();

        if ($existing && ! $this->option('force')) {
            $this->line("📅 Événement existant retrouvé → ID {$existing->id} (utilisez --force pour le recréer)");
            return $existing;
        }

        if ($existing && $this->option('force')) {
            $this->warn('  ↳ --force : suppression de l\'événement existant et de ses inscriptions...');
            if (! $isDryRun) {
                $existing->delete();
            }
        }

        $data = array_merge($this->eventData, [
            'description'     => $this->eventDescription,
            'program'         => $this->eventProgram,
            'starts_at'       => Carbon::parse($this->eventData['starts_at']),
            'ends_at'         => Carbon::parse($this->eventData['ends_at']),
            'created_by'      => User::role('super-admin')->value('id') ?? User::first()?->id,
            'registrations_count' => 0,
        ]);

        $this->line('📅 Création de l\'événement "' . $data['title'] . '"...');

        if ($isDryRun) {
            $this->line('  ↳ [dry-run] Événement non créé.');
            return null;
        }

        $event = Event::create($data);
        $this->info("  ✅ Événement créé → ID {$event->id}");

        return $event;
    }

    // ── Import d'une ligne ────────────────────────────────────────────────

    /**
     * @return 'member'|'guest'|'skipped'
     */
    private function importRow(array $row, ?Event $event, bool $isDryRun): string
    {
        $email     = strtolower(trim($row['email'] ?? ''));
        $firstName = trim($row['first_name'] ?? '');
        $lastName  = trim($row['last_name'] ?? '');
        $phone     = trim($row['phone_number'] ?? '') ?: null;
        $createdAt = isset($row['created_at']) ? Carbon::parse($row['created_at']) : now();

        if (empty($email)) {
            throw new \InvalidArgumentException('Email vide.');
        }

        // ── Matching membre existant ───────────────────────────────────────
        $user = User::where('email', $email)->first();

        if ($user) {
            return $this->importAsMember($user, $event, $createdAt, $isDryRun);
        }

        // ── Invité externe ─────────────────────────────────────────────────
        return $this->importAsGuest($firstName, $lastName, $email, $phone, $event, $createdAt, $isDryRun);
    }

    private function importAsMember(User $user, ?Event $event, Carbon $registeredAt, bool $isDryRun): string
    {
        if ($event === null) {
            return 'member';
        }

        $exists = $event->registrations()->where('user_id', $user->id)->exists();

        if ($exists) {
            $this->line("  · [membre] {$user->email} — déjà inscrit, ignoré");
            return 'skipped';
        }

        $this->line("  · [membre] {$user->email} → event_registrations");

        if (! $isDryRun) {
            $event->registrations()->create([
                'user_id'        => $user->id,
                'status'         => 'confirmed',
                'payment_status' => 'free',
                'registered_at'  => $registeredAt,
            ]);
        }

        return 'member';
    }

    private function importAsGuest(
        string $firstName,
        string $lastName,
        string $email,
        ?string $phone,
        ?Event $event,
        Carbon $registeredAt,
        bool $isDryRun
    ): string {
        if ($event === null) {
            return 'guest';
        }

        $exists = GuestRegistration::where('event_id', $event->id)->where('email', $email)->exists();

        if ($exists) {
            $this->line("  · [guest] {$email} — déjà inscrit, ignoré");
            return 'skipped';
        }

        $this->line("  · [guest] {$email} → guest_registrations");

        if (! $isDryRun) {
            GuestRegistration::create([
                'event_id'       => $event->id,
                'first_name'     => $firstName ?: 'Invité',
                'last_name'      => $lastName ?: '',
                'email'          => $email,
                'whatsapp'       => $phone,
                'status'         => 'confirmed',
                'payment_status' => 'free',
                'registered_at'  => $registeredAt,
            ]);
        }

        return 'guest';
    }

    // ── Lecture CSV ───────────────────────────────────────────────────────

    private function parseCsv(string $path): \Illuminate\Support\Collection
    {
        $handle = fopen($path, 'r');

        if ($handle === false) {
            throw new \RuntimeException("Impossible d'ouvrir le fichier : {$path}");
        }

        $headers = fgetcsv($handle);
        $rows    = collect();

        while (($data = fgetcsv($handle)) !== false) {
            if (count($data) === count($headers)) {
                $rows->push(array_combine($headers, $data));
            }
        }

        fclose($handle);

        // Filtrer uniquement les "approved"
        return $rows->filter(fn (array $r): bool => ($r['approval_status'] ?? '') === 'approved');
    }
}
