<?php

declare(strict_types=1);

namespace App\Services\Events;

use App\Enums\EventStatus;
use App\Models\Event;
use App\Models\EventPhoto;
use App\Models\User;
use App\Services\AssetService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;

class EventRecapService
{
    private const MAX_PHOTOS = 10;

    public function __construct(
        private readonly AssetService $assetService,
        private readonly NotificationService $notificationService,
    ) {}

    /**
     * Met à jour le résumé, le contenu et les liens vidéo du récapitulatif.
     */
    public function updateRecap(Event $event, array $data): Event
    {
        $event->update([
            'recap_summary'     => $data['recap_summary']     ?? null,
            'recap_content'     => $data['recap_content']     ?? null,
            'recap_video_url_1' => $data['recap_video_url_1'] ?? null,
            'recap_video_url_2' => $data['recap_video_url_2'] ?? null,
            'recap_video_url_3' => $data['recap_video_url_3'] ?? null,
        ]);

        return $event;
    }

    /**
     * Téléverse le document de récapitulatif (PDF/Word) et remplace l'ancien le cas échéant.
     */
    public function uploadDocument(Event $event, mixed $file, int $userId): Event
    {
        $filename = $this->assetService->upload(
            $file,
            'documents/events',
            'event-recap',
            $userId,
            $event->recap_document_path,
        );

        $event->update([
            'recap_document_path' => $filename,
            'recap_document_name' => $file->getClientOriginalName(),
        ]);

        return $event;
    }

    /**
     * Supprime le document de récapitulatif.
     */
    public function deleteDocument(Event $event): Event
    {
        if ($event->recap_document_path !== null) {
            $this->assetService->delete('documents/events', $event->recap_document_path);
        }

        $event->update([
            'recap_document_path' => null,
            'recap_document_name' => null,
        ]);

        return $event;
    }

    /**
     * Ajoute des photos au récapitulatif (10 maximum au total).
     *
     * @param  array<int, mixed>  $files
     *
     * @throws \Exception
     */
    public function addPhotos(Event $event, array $files, int $userId): void
    {
        $existingCount = $event->photos()->count();

        if ($existingCount + count($files) > self::MAX_PHOTOS) {
            throw new \Exception('Le nombre maximum de photos (' . self::MAX_PHOTOS . ') serait dépassé.');
        }

        $order = $event->photos()->max('order') ?? -1;

        foreach ($files as $file) {
            $filename = $this->assetService->upload($file, 'events/recap', 'event-photo', $userId);

            $order++;

            EventPhoto::create([
                'event_id' => $event->id,
                'path'     => $filename,
                'order'    => $order,
            ]);
        }
    }

    /**
     * Supprime une photo du récapitulatif.
     */
    public function deletePhoto(EventPhoto $photo): void
    {
        $this->assetService->delete('events/recap', $photo->path);

        $photo->delete();
    }

    /**
     * Réordonne les photos selon une liste d'identifiants ordonnés.
     *
     * @param  array<int, int>  $orderedIds
     */
    public function reorderPhotos(Event $event, array $orderedIds): void
    {
        DB::transaction(function () use ($event, $orderedIds) {
            foreach ($orderedIds as $position => $photoId) {
                EventPhoto::where('event_id', $event->id)
                    ->where('id', $photoId)
                    ->update(['order' => $position]);
            }
        });
    }

    /**
     * Publie le récapitulatif et notifie les participants confirmés/présents.
     *
     * @throws \Exception
     */
    public function publish(User $admin, Event $event): Event
    {
        if ($event->status !== EventStatus::Completed) {
            throw new \Exception('Le récapitulatif ne peut être publié que pour un événement terminé.');
        }

        $event->update([
            'recap_published_at' => now(),
            'recap_published_by' => $admin->id,
        ]);

        $registrants = $event->registrations()
            ->whereIn('status', ['confirmed', 'attended'])
            ->with('user')
            ->get();

        foreach ($registrants as $registration) {
            if ($registration->user) {
                $this->notificationService->sendEventRecapPublished($registration->user, $event);
            }
        }

        return $event;
    }

    /**
     * Dépublie le récapitulatif.
     */
    public function unpublish(Event $event): Event
    {
        $event->update([
            'recap_published_at' => null,
            'recap_published_by' => null,
        ]);

        return $event;
    }
}
