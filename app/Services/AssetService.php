<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AssetService
{
    /**
     * Stocke un fichier uploadé via le disque approprié.
     * Avatars → disque public (accès direct).
     * CVs → disque local (servis via CvController avec auth).
     *
     * @param  mixed  $file  UploadedFile ou Livewire TemporaryUploadedFile
     * @param  string  $folder  Sous-dossier (ex: avatars, cv)
     * @param  string  $prefix  Préfixe du nom de fichier
     * @param  int  $userId  ID de l'utilisateur
     * @param  string|null  $old  Ancien fichier à supprimer
     * @return string Nom du fichier sauvegardé
     */
    public function upload(
        mixed $file,
        string $folder,
        string $prefix,
        int $userId,
        ?string $old = null
    ): string {
        if ($old) {
            $this->delete($folder, $old);
        }

        // Extension basée sur le MIME réel, pas le nom fourni par le client
        $extension = $file->extension();
        $filename  = Str::lower("{$prefix}_{$userId}_" . time() . "_{$this->randomString()}.{$extension}");

        Storage::disk($this->diskFor($folder))->putFileAs($folder, $file, $filename);

        return $filename;
    }

    public function delete(string $folder, string $filename): void
    {
        Storage::disk($this->diskFor($folder))->delete("{$folder}/{$filename}");
    }

    public function url(string $folder, string $filename): string
    {
        return Storage::disk($this->diskFor($folder))->url("{$folder}/{$filename}");
    }

    /**
     * CVs stockés sur le disque local (privé), tout le reste sur public.
     */
    private function diskFor(string $folder): string
    {
        return match ($folder) {
            'cv'    => 'local',
            default => 'public',
        };
    }

    private function randomString(int $length = 6): string
    {
        return Str::lower(Str::random($length));
    }
}
