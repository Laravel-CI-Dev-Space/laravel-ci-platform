<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use App\Services\Events\EventMediaService;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class LaravelCiMeet01Seeder extends Seeder
{
    // ── IDs Google Drive des photos de l'événement ─────────────────────────
    private const DRIVE_PHOTO_IDS = [
        '1hY7Yh58lDcHgv0Rw8BwK17b9TUnHSpnk',
        '1WEcs2MoOsK0qv2EuvtWmTWkGvWlMHWzC',
        '1kG9fY9Prp8_zUeS93PKuPL9D8HFHbJZW',
        '15FLQ0AqSIWlWW6csN-K4ER3t6r6Y1ynV',
        '1skVk__OcUFtueskTaLsaaDw4EfZ9k4r4',
        '1Cq-Uur-VzHtSYfLAiqjEr1t6T_kzpIBS',
        '1rzHapflXyBkE3ERgk4L64gVchVmuXyp3',
        '1NbHaNz0vk4A3FQy6ByLZ4TPggFQtsCCN',
        '17aueHaLF0fB_zBhwQTJ5mcnN-peqXDIw',
        '1IT1BV-bs6GzL9KBdN_pkY2c-N-zQrObm',
        '1gEvlS7r5jDZT33ozDF6ZyBI149ciZRso',
        '1zSojlxtSj81V-KZpX_1Y9h5kwoCzQ6Ey',
        '1-zMg3VczeHW2daK9tOBKDttU3LwkAE1i',
        '1TMo3egab-ejhJWfpTwIlImTiSqlSjuy_',
    ];

    public function run(EventMediaService $mediaService): void
    {
        $creator = User::where('github_id', '167759591')->first()
            ?? User::first();

        if ($creator === null) {
            $this->command->warn('LaravelCiMeet01Seeder : aucun utilisateur trouvé, abandon.');
            return;
        }

        // ── 1. Sync cover + document vers le disk assets ───────────────────
        $coverPath  = null;
        $docPath    = $this->syncDocumentToAssets('documents/events/Design-program.pdf');

        // ── 2. Créer / mettre à jour l'événement ──────────────────────────
        $event = Event::updateOrCreate(
            ['slug' => 'laravel-ci-meet-01'],
            [
                'created_by'   => $creator->id,
                'title'        => 'Laravel CI Meet #01',
                'description'  => <<<'HTML'
                    <h2>La première grande rencontre de la communauté Laravel CI</h2>
                    <p>
                        Le <strong>12 septembre 2026</strong>, Epitech Abidjan accueille la toute première édition
                        du <strong>Laravel CI Meet</strong> — un événement inédit en Côte d'Ivoire dédié à l'écosystème
                        Laravel et à l'intelligence artificielle.
                    </p>
                    <p>
                        Autour du thème <em>« L'IA au service des développeurs Laravel »</em>, des speakers de renom
                        partagent leurs expériences, démos live et retours terrain devant une salle de passionnés.
                    </p>
                    <h3>Pourquoi participer ?</h3>
                    <ul>
                        <li>Découvrir comment intégrer l'IA dans vos projets Laravel concrets</li>
                        <li>Rencontrer d'autres développeurs ivoiriens passionnés par Laravel</li>
                        <li>Assister à des démos live sur des projets réels</li>
                        <li>Participer à une table ronde sur l'avenir du développement augmenté</li>
                        <li>Rejoindre officiellement la communauté Laravel CI</li>
                    </ul>
                    <p><strong>Entrée gratuite — inscription obligatoire, places limitées.</strong></p>
                    HTML,

                'program' => <<<'HTML'
                    <ul>
                        <li><strong>09h30</strong> — Accueil des participants</li>
                        <li><strong>10h15</strong> — <em>Wilson Kouassi</em> · Le Hub Laravel CI : vision, communauté et avenir</li>
                        <li><strong>10h30</strong> — Ouverture officielle de l'événement</li>
                        <li><strong>11h00</strong> — <em>Boubacar LY</em> · Votre API a un nouveau client : l'agent IA</li>
                        <li><strong>11h45</strong> — <em>Rygel Louv</em> · Du développeur assisté à l'équipe augmentée</li>
                        <li><strong>12h30</strong> — Pause déjeuner &amp; networking</li>
                        <li><strong>14h00</strong> — <em>Mahamadou Diaby (KEPSON)</em> · IA et productivité Laravel</li>
                        <li><strong>14h45</strong> — <em>Doro Gueye</em> · From App to Agent — démo live</li>
                        <li><strong>15h30</strong> — Pause</li>
                        <li><strong>16h00</strong> — Table ronde : l'IA va-t-elle remplacer le développeur ?</li>
                        <li><strong>17h30</strong> — Photo de famille &amp; networking</li>
                        <li><strong>18h15</strong> — Clôture officielle</li>
                    </ul>
                    HTML,

                'type'                       => 'conference',
                'location'                   => 'Epitech Abidjan, Riviera 2, Abidjan',
                'online_url'                 => null,
                'cover_image'                => $coverPath,
                'starts_at'                  => '2026-09-12 10:30:00',
                'ends_at'                    => '2026-09-12 18:15:00',
                'capacity'                   => 100,
                'waitlist_enabled'           => false,
                'status'                     => 'completed',
                'is_paid'                    => false,
                'price'                      => null,
                'currency'                   => null,
                'promo_code'                 => null,
                'promo_discount_type'        => null,
                'promo_discount_value'       => null,
                'promo_expires_at'           => null,
                'promo_max_uses'             => null,
                'promo_uses_count'           => 0,
                'ticketing_enabled'          => true,
                'ticket_prefix'              => 'LCI',
                'guest_registration_enabled' => true,
                'reminder_7d_sent'           => true,
                'reminder_1d_sent'           => true,

                // ── Recap post-événement ───────────────────────────────────
                'recap_summary' => 'Le 12 septembre 2026, la communauté Laravel CI a tenu sa première édition du Laravel CI Meet à Epitech Abidjan. Plus de 80 développeurs réunis autour du thème « L\'IA au service des développeurs Laravel » ont exploré, débattu et expérimenté pendant une journée intense de talks, de démos live et d\'échanges en panel — marquant ainsi le lancement officiel de la communauté.',

                'recap_content' => <<<'HTML'
                    <h2>Une première édition historique pour la communauté</h2>
                    <p>
                        Le <strong>12 septembre 2026</strong>, Epitech Abidjan a accueilli la toute première édition du
                        <strong>Laravel CI Meet</strong>, événement phare de la communauté Laravel CI de Côte d'Ivoire.
                        Officiellement lancée aux environs de <strong>10h30</strong>, la journée s'est terminée à
                        <strong>18h15</strong> dans une ambiance chaleureuse et fraternelle — signe que quelque chose
                        de fort venait de naître.
                    </p>

                    <h2>Le programme de la journée</h2>
                    <p>Autour du thème central <em>« L'IA au service des développeurs Laravel »</em>, cinq talks
                    et une table ronde ont rythmé la journée :</p>
                    <ul>
                        <li>
                            <strong>Wilson Kouassi</strong> — <em>Le Hub Laravel CI : vision, communauté et avenir</em><br>
                            Présentation du projet communautaire, de sa raison d'être et de la feuille de route pour
                            les mois à venir.
                        </li>
                        <li>
                            <strong>Boubacar LY</strong> — <em>Votre API a un nouveau client : l'agent IA</em><br>
                            Comment concevoir et adapter une API Laravel pour être consommée par un agent IA autonome —
                            patterns, authentification, contrats sémantiques.
                        </li>
                        <li>
                            <strong>Rygel Louv</strong> — <em>Du développeur assisté à l'équipe augmentée</em><br>
                            Retour d'expérience sur l'intégration d'outils IA dans le quotidien d'une équipe Laravel,
                            des premiers copilotes aux workflows entièrement augmentés.
                        </li>
                        <li>
                            <strong>Mahamadou Diaby (KEPSON)</strong> — <em>IA et productivité Laravel</em><br>
                            Cas concrets d'utilisation de l'IA pour accélérer le développement, réduire la dette
                            technique et améliorer la qualité du code en production.
                        </li>
                        <li>
                            <strong>Doro Gueye</strong> — <em>From App to Agent : démo live</em><br>
                            Démonstration en direct de son projet open-source
                            <a href="https://github.com/hadjidoro/from-app-to-agent">from-app-to-agent</a> :
                            un agent IA qui automatise l'analyse de données, la formulation de réponses et le
                            traitement de fichiers.
                        </li>
                    </ul>

                    <h2>La table ronde : l'IA va-t-elle remplacer le développeur ?</h2>
                    <p>
                        En fin de journée, une table ronde a réuni speakers et participants pour débattre de la place
                        de l'IA dans le métier de développeur. Les échanges ont été vifs, nuancés et profondément
                        humains — la conclusion unanime : l'IA est un levier, pas un substitut.
                        Ce qui change, c'est la façon de penser et de concevoir.
                    </p>

                    <h2>L'ambiance et la communauté</h2>
                    <p>
                        Au-delà des talks, c'est la qualité des rencontres qui a marqué les esprits.
                        Des participants venus de tout le pays, des échanges spontanés dans les couloirs,
                        des retours enthousiastes sur les réseaux sociaux — la communauté Laravel CI a prouvé
                        qu'elle répondait à un besoin réel.
                    </p>
                    <blockquote>
                        « Future AKademy est fier de soutenir une initiative qui allie excellence technique
                        et partage de la connaissance. »
                    </blockquote>

                    <h2>Et maintenant ?</h2>
                    <p>
                        Cette première édition n'est qu'un début. La communauté Laravel CI continue de grandir
                        avec des rencontres régulières, des ressources en ligne et une prochaine édition déjà
                        dans les cartons. Rejoins-nous sur le Hub pour rester informé.
                    </p>
                    HTML,

                'recap_document_name' => 'Programme officiel — Laravel CI Meet #01',
                'recap_document_path' => $docPath,
                'recap_published_at'  => now(),
                'recap_published_by'  => $creator->id,
            ]
        );

        $this->command->info("LaravelCiMeet01Seeder : événement '{$event->title}' OK.");

        // ── 3. Import des photos depuis Google Drive ───────────────────────
        $this->importDrivePhotos($event, $mediaService);
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    /**
     * Copie la cover depuis public/assets vers le disk assets.
     * Retourne le chemin stocké dans la DB (relatif au disk).
     */
    private function syncCoverToAssets(): ?string
    {
        $local  = public_path('assets/web/img/mascot.png');
        $target = 'web/img/events/laravel-ci-meet-01-cover.png';

        if (! file_exists($local)) {
            $this->command->warn('  Cover locale introuvable — cover_image laissée à null.');
            return null;
        }

        if (! Storage::disk('assets')->exists($target)) {
            Storage::disk('assets')->put($target, file_get_contents($local));
        }

        return $target;
    }

    /**
     * Sync un fichier local public/assets vers le disk assets.
     * Retourne le basename stocké dans la DB (la méthode recapDocumentUrl()
     * préfixe déjà "documents/events/").
     */
    private function syncDocumentToAssets(string $relativePath): ?string
    {
        $local  = public_path('assets/' . $relativePath);
        $target = $relativePath;                          // chemin complet sur disk assets

        if (! file_exists($local)) {
            $this->command->warn("  Document local introuvable : {$local}");
            return null;
        }

        if (! Storage::disk('assets')->exists($target)) {
            Storage::disk('assets')->put($target, file_get_contents($local));
        }

        // recapDocumentUrl() fait : 'documents/events/' . $path
        // → on stocke seulement le basename pour éviter la double-préfixation
        return basename($relativePath);
    }

    /**
     * Télécharge chaque photo depuis Drive et l'upload via EventMediaService.
     * Passe silencieusement les photos déjà présentes (idempotent).
     */
    private function importDrivePhotos(Event $event, EventMediaService $service): void
    {
        $existing = $event->media()->pluck('original_name')->toArray();
        $ok = 0; $skipped = 0; $errors = 0;

        foreach (self::DRIVE_PHOTO_IDS as $fileId) {
            $url = "https://drive.google.com/uc?export=download&id={$fileId}&confirm=1";

            $this->command->line("  → Drive {$fileId}…");

            try {
                $response = Http::timeout(90)
                    ->withHeaders(['User-Agent' => 'Mozilla/5.0'])
                    ->get($url);

                if (! $response->successful()) {
                    $this->command->warn("    ✗ HTTP {$response->status()}");
                    $errors++;
                    continue;
                }

                // Nom depuis Content-Disposition ou fallback
                preg_match('/filename[^;=\n]*=([\'"]*)(.*)\1/', $response->header('Content-Disposition') ?? '', $m);
                $originalName = isset($m[2]) && $m[2] !== ''
                    ? preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($m[2]))
                    : "{$fileId}.jpg";

                if (in_array($originalName, $existing, true)) {
                    $this->command->line("    ↷ Déjà importé : {$originalName}");
                    $skipped++;
                    continue;
                }

                $tmp  = sys_get_temp_dir() . '/' . uniqid('lci_', true) . '_' . $originalName;
                file_put_contents($tmp, $response->body());

                $mime = mime_content_type($tmp) ?: 'image/jpeg';
                $file = new UploadedFile($tmp, $originalName, $mime, null, true);

                $service->uploadPhoto($event, $file);

                @unlink($tmp);
                $ok++;
                $this->command->info("    ✓ {$originalName}");

            } catch (\Exception $e) {
                $this->command->error("    ✗ {$fileId} : {$e->getMessage()}");
                $errors++;
            }
        }

        $this->command->newLine();
        $this->command->info("Photos : {$ok} importées · {$skipped} passées · {$errors} erreurs");
    }
}
