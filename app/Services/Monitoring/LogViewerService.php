<?php

declare(strict_types=1);

namespace App\Services\Monitoring;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

class LogViewerService
{
    private const LEVELS = ['emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug'];

    private const ENTRY_PATTERN = '/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (\S+)\.(\w+): (.*)$/';

    public function path(): string
    {
        return storage_path('logs/laravel.log');
    }

    public function exists(): bool
    {
        return is_file($this->path());
    }

    /**
     * @return array{totalLines: int, totalEntries: int, fileSize: string, lastModified: ?Carbon, levelCounts: array<string, int>}
     */
    public function getStats(): array
    {
        if (! $this->exists()) {
            return [
                'totalLines'   => 0,
                'totalEntries' => 0,
                'fileSize'     => '0 o',
                'lastModified' => null,
                'levelCounts'  => array_fill_keys(self::LEVELS, 0),
            ];
        }

        $path = $this->path();

        // Coûteux (10 process spawn par appel) : mis en cache tant que le
        // fichier de log n'a pas changé, pour supporter le polling 10s du widget.
        // Le timestamp est stocké en int (pas en Carbon) pour éviter les soucis
        // de unserialize() d'objets Carbon selon le driver de cache.
        $stats = Cache::remember(
            "log_viewer_stats:{$this->path()}:{$this->cacheVersion()}",
            now()->addSeconds(10),
            function () use ($path): array {
                $totalLines   = (int) trim(Process::run(['wc', '-l', $path])->output());
                $totalEntries = (int) trim(Process::run(['grep', '-cE', '^\[[0-9]{4}-', $path])->output());

                $levelCounts = [];

                foreach (self::LEVELS as $level) {
                    $levelCounts[$level] = (int) trim(Process::run(['grep', '-icE', '\.' . $level . ':', $path])->output());
                }

                return [
                    'totalLines'       => $totalLines,
                    'totalEntries'     => $totalEntries,
                    'fileSize'         => self::formatBytes((int) filesize($path)),
                    'lastModifiedTime' => (int) filemtime($path),
                    'levelCounts'      => $levelCounts,
                ];
            }
        );

        $stats['lastModified'] = Carbon::createFromTimestamp($stats['lastModifiedTime']);
        unset($stats['lastModifiedTime']);

        return $stats;
    }

    /** Identifiant qui change dès que le fichier de log est modifié (taille + mtime). */
    private function cacheVersion(): string
    {
        return filesize($this->path()) . ':' . filemtime($this->path());
    }

    /**
     * Niveaux de log disponibles, pour les filtres.
     *
     * @return array<int, string>
     */
    public function levels(): array
    {
        return self::LEVELS;
    }

    /**
     * @return array<int, array{timestamp: string, channel: string, level: string, message: string, extraLines: int}>
     */
    public function getRecentEntries(int $limit = 50, int $tailLines = 5000, ?string $search = null, ?string $level = null): array
    {
        if (! $this->exists()) {
            return [];
        }

        $entries = $this->parseEntries($tailLines);

        if ($level !== null && $level !== '') {
            $entries = array_values(array_filter(
                $entries,
                fn (array $entry): bool => $entry['level'] === $level
            ));
        }

        if ($search !== null && $search !== '') {
            $needle = mb_strtolower($search);

            $entries = array_values(array_filter(
                $entries,
                fn (array $entry): bool => str_contains(mb_strtolower($entry['fullText']), $needle)
                    || str_contains(mb_strtolower($entry['channel']), $needle)
            ));
        }

        $entries = array_map(function (array $entry): array {
            unset($entry['fullText']);

            return $entry;
        }, $entries);

        return array_reverse(array_slice($entries, -$limit));
    }

    /**
     * Lit et parse les `$tailLines` dernières lignes du fichier de log.
     * Coûteux (spawn `tail` + parsing ligne à ligne) : mis en cache tant que
     * le fichier n'a pas changé, pour supporter le polling du widget.
     *
     * @return array<int, array{timestamp: string, channel: string, level: string, message: string, fullText: string, extraLines: int}>
     */
    private function parseEntries(int $tailLines): array
    {
        return Cache::remember(
            "log_viewer_entries:{$this->path()}:{$tailLines}:{$this->cacheVersion()}",
            now()->addSeconds(10),
            function () use ($tailLines): array {
                $output = Process::run(['tail', '-n', (string) $tailLines, $this->path()])->output();

                $entries = [];
                $current = null;

                foreach (explode("\n", $output) as $line) {
                    if (preg_match(self::ENTRY_PATTERN, $line, $matches)) {
                        if ($current !== null) {
                            $entries[] = $current;
                        }

                        $current = [
                            'timestamp'  => $matches[1],
                            'channel'    => $matches[2],
                            'level'      => strtolower($matches[3]),
                            'message'    => Str::limit($matches[4], 200),
                            'fullText'   => $matches[4],
                            'extraLines' => 0,
                        ];
                    } elseif ($current !== null && trim($line) !== '') {
                        $current['extraLines']++;
                        $current['fullText'] .= ' ' . $line;
                    }
                }

                if ($current !== null) {
                    $entries[] = $current;
                }

                return $entries;
            }
        );
    }

    private static function formatBytes(int $bytes): string
    {
        $units = ['o', 'Ko', 'Mo', 'Go', 'To'];
        $i     = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 1) . ' ' . $units[$i];
    }
}
