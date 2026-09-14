<?php

declare(strict_types=1);

namespace App\Http\Controllers\Events;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventMedia;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use STS\ZipStream\ZipStream;

class EventMediaDownloadController extends Controller
{
    /**
     * Télécharge un seul média (photo ou vidéo) directement depuis R2.
     */
    public function single(Event $event, EventMedia $media): Response|\Illuminate\Http\RedirectResponse
    {
        abort_unless($media->event_id === $event->id, 404);

        // Redirect vers l'URL R2 publique — le navigateur gère le téléchargement
        return redirect()->away(
            Storage::disk($media->disk)->url($media->path)
        );
    }

    /**
     * Génère et stream un ZIP de plusieurs médias sélectionnés.
     *
     * Body JSON attendu : { "ids": [1, 2, 3, ...] }
     */
    public function zip(Request $request, Event $event): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $validated = $request->validate([
            'ids'   => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
        ]);

        $media = EventMedia::where('event_id', $event->id)
            ->whereIn('id', $validated['ids'])
            ->orderBy('order')
            ->get();

        abort_if($media->isEmpty(), 404, 'Aucun média trouvé.');

        $zipName = 'laravel-ci-' . $event->slug . '-recap.zip';

        return ZipStream::create($zipName, function (ZipStream $zip) use ($media): void {
            foreach ($media as $item) {
                $stream = Storage::disk($item->disk)->readStream($item->path);

                if ($stream === null) {
                    continue;
                }

                // Nom de fichier dans le ZIP : type/nom-original
                $folder   = $item->isPhoto() ? 'photos' : 'videos';
                $filename = $folder . '/' . $item->order . '_' . $item->original_name;

                $zip->addFileFromStream($filename, $stream);
            }
        });
    }

    /**
     * ZIP de tous les médias d'un événement (sans sélection).
     */
    public function zipAll(Event $event): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $media = $event->media()->orderBy('type')->orderBy('order')->get();

        abort_if($media->isEmpty(), 404, 'Cet événement n\'a pas encore de médias.');

        $zipName = 'laravel-ci-' . $event->slug . '-medias-complets.zip';

        return ZipStream::create($zipName, function (ZipStream $zip) use ($media): void {
            foreach ($media as $item) {
                $stream = Storage::disk($item->disk)->readStream($item->path);

                if ($stream === null) {
                    continue;
                }

                $folder   = $item->isPhoto() ? 'photos' : 'videos';
                $filename = $folder . '/' . $item->order . '_' . $item->original_name;

                $zip->addFileFromStream($filename, $stream);
            }
        });
    }
}
