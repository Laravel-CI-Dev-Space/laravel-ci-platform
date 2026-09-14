<?php

declare(strict_types=1);

namespace App\Services\Events;

use App\Enums\EventMediaType;
use App\Models\Event;
use App\Models\EventMedia;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class EventMediaService
{
    private const DISK             = 'r2';
    private const THUMB_WIDTH      = 400;   // px — thumbnail pour la grille
    private const THUMB_HEIGHT     = 300;   // px
    private const MAX_VIDEO_BYTES  = 524_288_000; // 500 Mo

    // ── Upload ─────────────────────────────────────────────────────────────

    /**
     * Upload une photo vers R2 + génère son thumbnail.
     *
     * @throws \Exception si le fichier n'est pas une image valide
     */
    public function uploadPhoto(Event $event, UploadedFile $file, ?string $caption = null): EventMedia
    {
        $this->guardPhoto($file);

        $baseName = $this->uniqueName($file);
        $dir      = "events/{$event->id}/photos";
        $thumbDir = "events/{$event->id}/thumbs";

        // Original → R2
        $path = Storage::disk(self::DISK)->putFileAs($dir, $file, $baseName);

        // Thumbnail → R2
        $thumbPath = $this->generateAndUploadThumbnail($file, $thumbDir, $baseName);

        $order = (EventMedia::where('event_id', $event->id)->max('order') ?? -1) + 1;

        return EventMedia::create([
            'event_id'       => $event->id,
            'type'           => EventMediaType::Photo,
            'disk'           => self::DISK,
            'path'           => $path,
            'original_name'  => $file->getClientOriginalName(),
            'mime_type'      => $file->getMimeType(),
            'file_size'      => $file->getSize(),
            'thumbnail_path' => $thumbPath,
            'caption'        => $caption,
            'order'          => $order,
        ]);
    }

    /**
     * Upload une vidéo MP4 vers R2 (max 500 Mo).
     *
     * @throws \Exception si le fichier dépasse la limite ou n'est pas MP4
     */
    public function uploadVideo(Event $event, UploadedFile $file, ?string $caption = null): EventMedia
    {
        $this->guardVideo($file);

        $baseName = $this->uniqueName($file);
        $dir      = "events/{$event->id}/videos";

        $path = Storage::disk(self::DISK)->putFileAs($dir, $file, $baseName);

        $order = (EventMedia::where('event_id', $event->id)->max('order') ?? -1) + 1;

        return EventMedia::create([
            'event_id'      => $event->id,
            'type'          => EventMediaType::Video,
            'disk'          => self::DISK,
            'path'          => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type'     => $file->getMimeType(),
            'file_size'     => $file->getSize(),
            'caption'       => $caption,
            'order'         => $order,
        ]);
    }

    // ── Suppression ────────────────────────────────────────────────────────

    public function delete(EventMedia $media): void
    {
        Storage::disk($media->disk)->delete($media->path);

        if ($media->thumbnail_path) {
            Storage::disk($media->disk)->delete($media->thumbnail_path);
        }

        $media->delete();
    }

    public function deleteAllForEvent(Event $event): void
    {
        $event->media()->each(fn (EventMedia $m) => $this->delete($m));
    }

    // ── Réordonnage ────────────────────────────────────────────────────────

    /**
     * @param  array<int, int>  $orderedIds  IDs dans le nouvel ordre
     */
    public function reorder(Event $event, array $orderedIds): void
    {
        DB::transaction(function () use ($event, $orderedIds): void {
            foreach ($orderedIds as $position => $mediaId) {
                EventMedia::where('event_id', $event->id)
                    ->where('id', $mediaId)
                    ->update(['order' => $position]);
            }
        });
    }

    // ── Statistiques ───────────────────────────────────────────────────────

    public function stats(Event $event): array
    {
        $media = $event->media()->get();

        return [
            'total'       => $media->count(),
            'photos'      => $media->where('type', EventMediaType::Photo)->count(),
            'videos'      => $media->where('type', EventMediaType::Video)->count(),
            'total_size'  => $media->sum('file_size'),
        ];
    }

    // ── Privé ──────────────────────────────────────────────────────────────

    private function guardPhoto(UploadedFile $file): void
    {
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

        if (! in_array($file->getMimeType(), $allowed, true)) {
            throw new \InvalidArgumentException(
                "Format non supporté : {$file->getMimeType()}. Photos acceptées : JPEG, PNG, WebP, GIF."
            );
        }
    }

    private function guardVideo(UploadedFile $file): void
    {
        if ($file->getMimeType() !== 'video/mp4') {
            throw new \InvalidArgumentException(
                "Seules les vidéos MP4 sont acceptées. Format reçu : {$file->getMimeType()}."
            );
        }

        if ($file->getSize() > self::MAX_VIDEO_BYTES) {
            $maxMo = self::MAX_VIDEO_BYTES / 1_048_576;
            throw new \InvalidArgumentException(
                "La vidéo dépasse la limite de {$maxMo} Mo. Compressez-la avec HandBrake avant d'uploader."
            );
        }
    }

    private function uniqueName(UploadedFile $file): string
    {
        $ext = $file->getClientOriginalExtension() ?: 'bin';

        return Str::uuid() . '.' . $ext;
    }

    /**
     * Génère un thumbnail redimensionné et l'upload vers R2.
     * Retourne le chemin dans le bucket.
     */
    private function generateAndUploadThumbnail(
        UploadedFile $file,
        string $thumbDir,
        string $baseName
    ): string {
        // Nom du thumbnail en .webp pour réduire la taille
        $thumbName = pathinfo($baseName, PATHINFO_FILENAME) . '_thumb.webp';

        $image = Image::read($file->getRealPath())
            ->cover(self::THUMB_WIDTH, self::THUMB_HEIGHT);

        $encoded = $image->toWebp(quality: 80);

        $thumbPath = $thumbDir . '/' . $thumbName;

        Storage::disk(self::DISK)->put($thumbPath, $encoded->toString(), 'public');

        return $thumbPath;
    }
}
