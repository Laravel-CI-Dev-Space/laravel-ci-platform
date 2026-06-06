<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class FontAwesomeService
{
    private const BUNDLED_PATH = 'data/fontawesome-icons.json';

    private const DOWNLOADED_PATH = 'fontawesome/catalog.json';

    private static ?array $catalog = null;

    private static ?array $index = null;

    /**
     * Returns options for a Filament searchable Select.
     * Format: ['fa-solid fa-users' => 'Users (solid)']
     */
    public function forSelect(string $query = '', int $limit = 50): array
    {
        $catalog = $this->catalog();
        $query   = strtolower(trim($query));

        if ($query === '') {
            $items = array_slice($catalog, 0, $limit);
        } else {
            $items = [];
            foreach ($catalog as $icon) {
                if (
                    str_contains(strtolower($icon['l']), $query)
                    || str_contains($icon['c'], $query)
                ) {
                    $items[] = $icon;
                    if (count($items) >= $limit) {
                        break;
                    }
                }
            }
        }

        $result = [];
        foreach ($items as $icon) {
            $result[$icon['c']] = $this->optionLabel($icon['c'], $icon['l']);
        }

        return $result;
    }

    /**
     * Returns the plain-text label for a stored icon class.
     */
    public function label(string $iconClass): string
    {
        $idx = $this->index();

        if (isset($idx[$iconClass])) {
            return $idx[$iconClass];
        }

        // Fallback: derive from the class string
        $parts = explode(' ', $iconClass);
        $name  = end($parts);

        return ucwords(str_replace(['fa-', '-'], ['', ' '], $name));
    }

    /**
     * Returns an HtmlString icon preview for use in helperText or Placeholder.
     */
    public function preview(string $iconClass): HtmlString
    {
        $escaped = htmlspecialchars($iconClass, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $label   = htmlspecialchars($this->label($iconClass), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        return new HtmlString(
            '<span style="display:inline-flex;align-items:center;gap:.75rem;padding:.4rem 0">'
            . '<i class="' . $escaped . '" style="font-size:1.6rem;width:2rem;text-align:center;color:var(--primary-600,#ea580c)"></i>'
            . '<code style="font-size:.8rem;background:#f1f5f9;padding:.2rem .5rem;border-radius:.3rem;color:#475569">'
            . $escaped
            . '</code>'
            . '<span style="color:#64748b;font-size:.85rem">' . $label . '</span>'
            . '</span>'
        );
    }

    /**
     * Downloads the full FA 6 Free icon catalog from GitHub and stores it locally.
     * Returns the total number of icons stored.
     */
    public function downloadCatalog(): int
    {
        $url = 'https://raw.githubusercontent.com/FortAwesome/Font-Awesome/6.x/metadata/icons.json';

        $context = stream_context_create(['http' => ['timeout' => 30]]);
        $raw     = @file_get_contents($url, false, $context);

        if ($raw === false) {
            throw new \RuntimeException('Failed to download FA icons catalog. Check your internet connection.');
        }

        $data = json_decode($raw, true);

        if (! is_array($data)) {
            throw new \RuntimeException('Invalid JSON received from FA catalog.');
        }

        $styleMap = ['solid' => 'fa-solid', 'regular' => 'fa-regular', 'brands' => 'fa-brands'];
        $catalog  = [];

        foreach ($data as $name => $entry) {
            $label = $entry['label'] ?? ucwords(str_replace('-', ' ', (string) $name));
            foreach ($entry['styles'] ?? [] as $style) {
                if (! isset($styleMap[$style])) {
                    continue;
                }
                $catalog[] = ['c' => $styleMap[$style] . ' fa-' . $name, 'l' => $label];
            }
        }

        usort($catalog, fn (array $a, array $b): int => strcmp($a['l'], $b['l']));

        Storage::put(self::DOWNLOADED_PATH, json_encode($catalog, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));

        self::$catalog = null;
        self::$index   = null;

        return count($catalog);
    }

    public function hasCatalog(): bool
    {
        return Storage::exists(self::DOWNLOADED_PATH)
            || file_exists(resource_path(self::BUNDLED_PATH));
    }

    // ─── Private helpers ─────────────────────────────────────────────────────

    private function optionLabel(string $class, string $label): string
    {
        $style = match (true) {
            str_starts_with($class, 'fa-brands')  => 'brands',
            str_starts_with($class, 'fa-regular') => 'regular',
            default                               => 'solid',
        };

        return $label . ' (' . $style . ')';
    }

    private function catalog(): array
    {
        if (self::$catalog !== null) {
            return self::$catalog;
        }

        if (Storage::exists(self::DOWNLOADED_PATH)) {
            try {
                $json          = Storage::get(self::DOWNLOADED_PATH);
                self::$catalog = json_decode($json, true) ?? [];

                return self::$catalog;
            } catch (\Throwable) {
            }
        }

        $bundled = resource_path(self::BUNDLED_PATH);
        if (file_exists($bundled)) {
            self::$catalog = json_decode(file_get_contents($bundled), true) ?? [];

            return self::$catalog;
        }

        self::$catalog = [];

        return self::$catalog;
    }

    private function index(): array
    {
        if (self::$index !== null) {
            return self::$index;
        }

        self::$index = [];
        foreach ($this->catalog() as $icon) {
            self::$index[$icon['c']] = $icon['l'];
        }

        return self::$index;
    }
}
