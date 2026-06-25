<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\MemberCard;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

class MemberCardExporter
{
    /**
     * Génère un PNG de la carte membre et le retourne en bytes.
     *
     * Flux :
     *  1. Rend le template Blade dans un fichier HTML temporaire
     *  2. Appelle le script Puppeteer via Process
     *  3. Lit le PNG résultant, nettoie les fichiers temp, retourne les bytes
     */
    public function toPng(MemberCard $card): string
    {
        $card->load(['user.profile.grade', 'user']);

        $width  = config('member-card.width', 800);
        $height = config('member-card.height', 450);

        $html     = view($card->templateView(), compact('card'))->render();
        $tmpHtml  = tempnam(sys_get_temp_dir(), 'lci_card_') . '.html';
        $tmpPng   = tempnam(sys_get_temp_dir(), 'lci_card_') . '.png';

        try {
            file_put_contents($tmpHtml, $html);

            $script = base_path('resources/js/card-screenshot.js');
            $node   = config('member-card.node_binary', 'node');

            $result = Process::timeout(30)
                ->run([$node, $script, $tmpHtml, $tmpPng, (string) $width, (string) $height]);

            if (! $result->successful() || ! file_exists($tmpPng)) {
                throw new \RuntimeException(
                    'Card screenshot failed: ' . $result->errorOutput()
                );
            }

            return file_get_contents($tmpPng);

        } finally {
            @unlink($tmpHtml);
            @unlink($tmpPng);
        }
    }

    /**
     * Génère et stocke le PNG dans storage/public/member-cards/{id}.png.
     * Retourne le path relatif au disque public.
     */
    public function store(MemberCard $card): string
    {
        $png  = $this->toPng($card);
        $path = "member-cards/{$card->id}.png";

        Storage::disk('public')->put($path, $png);

        return $path;
    }
}
