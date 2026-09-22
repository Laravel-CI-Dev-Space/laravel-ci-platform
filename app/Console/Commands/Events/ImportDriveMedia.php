<?php

declare(strict_types=1);

namespace App\Console\Commands\Events;

use App\Models\Event;
use App\Services\Events\EventMediaService;
use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;

class ImportDriveMedia extends Command
{
    protected $signature = 'events:import-drive-media
                            {event : Slug de l\'événement}
                            {--ids= : IDs Google Drive séparés par des virgules}
                            {--captions= : Légendes séparées par des | (même ordre que les IDs)}
                            {--skip-existing : Passer les fichiers déjà importés (par original_name)}';

    protected $description = 'Importe des médias depuis Google Drive vers la galerie d\'un événement R2';

    public function handle(EventMediaService $service): int
    {
        $event = Event::where('slug', $this->argument('event'))->firstOrFail();
        $ids   = array_filter(array_map('trim', explode(',', $this->option('ids') ?? '')));

        if (empty($ids)) {
            $this->error('Aucun ID Drive fourni via --ids=');
            return self::FAILURE;
        }

        $captions = $this->option('captions')
            ? array_map('trim', explode('|', $this->option('captions')))
            : [];

        $existing = $this->option('skip-existing')
            ? $event->media()->pluck('original_name')->toArray()
            : [];

        $ok = 0; $skipped = 0; $errors = 0;

        foreach ($ids as $i => $fileId) {
            $caption = $captions[$i] ?? null;
            $url     = "https://drive.google.com/uc?export=download&id={$fileId}&confirm=1";

            $this->line("→ Téléchargement {$fileId}…");

            try {
                $response = Http::timeout(60)->withHeaders([
                    'User-Agent' => 'Mozilla/5.0',
                ])->get($url);

                if (! $response->successful()) {
                    $this->warn("  ✗ HTTP {$response->status()} pour {$fileId}");
                    $errors++;
                    continue;
                }

                // Détecter le nom depuis le header Content-Disposition
                $disposition = $response->header('Content-Disposition') ?? '';
                preg_match('/filename[^;=\n]*=([\'"]*)(.*)\1/', $disposition, $matches);
                $originalName = $matches[2] ?? "{$fileId}.jpg";
                $originalName = preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($originalName));

                if (in_array($originalName, $existing, true)) {
                    $this->line("  ↷ Déjà importé : {$originalName}");
                    $skipped++;
                    continue;
                }

                // Écrire dans un fichier temporaire
                $tmpPath = sys_get_temp_dir() . '/' . uniqid('drive_', true) . '_' . $originalName;
                file_put_contents($tmpPath, $response->body());

                $mime = mime_content_type($tmpPath) ?: 'image/jpeg';

                $file = new UploadedFile($tmpPath, $originalName, $mime, null, true);

                $isVideo = in_array($mime, ['video/mp4', 'video/mpeg', 'video/quicktime'], true);

                if ($isVideo) {
                    $service->uploadVideo($event, $file, $caption);
                } else {
                    $service->uploadPhoto($event, $file, $caption);
                }

                @unlink($tmpPath);
                $ok++;
                $this->info("  ✓ {$originalName}");

            } catch (\Exception $e) {
                $this->error("  ✗ Erreur {$fileId} : {$e->getMessage()}");
                $errors++;
            }
        }

        $this->newLine();
        $this->info("Import terminé — {$ok} OK · {$skipped} passés · {$errors} erreurs");

        return $errors > 0 ? self::FAILURE : self::SUCCESS;
    }
}
