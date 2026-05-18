<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class AssetService
{
    /**
     * Déplace un fichier uploadé vers public/assets/{folder}
     * Gère les espaces et caractères spéciaux dans les chemins.
     *
     * @param  UploadedFile|object $file   Fichier Livewire ou UploadedFile
     * @param  string              $folder Sous-dossier dans public/assets (ex: avatars, cv)
     * @param  string              $prefix Préfixe du nom de fichier (ex: avatar, cv)
     * @param  int                 $userId ID de l'utilisateur
     * @param  string|null         $old    Ancien fichier à supprimer
     * @return string              Nom du fichier sauvegardé
     */
    public function upload(
        mixed $file,
        string $folder,
        string $prefix,
        int $userId,
        ?string $old = null
    ): string {
        // Supprimer l'ancien fichier si existant
        if ($old) {
            $this->delete($folder, $old);
        }

        // Construire le chemin de destination proprement
        $destination = $this->ensureDirectory($folder);

        // Générer un nom de fichier unique et sans espaces
        $extension = $file->getClientOriginalExtension();
        $filename  = Str::lower("{$prefix}_{$userId}_" . time() . "_{$this->randomString()}.{$extension}");

        // Déplacer le fichier
        $file->move($destination, $filename);

        return $filename;
    }

    /**
     * Supprime un fichier dans public/assets/{folder}
     */
    public function delete(string $folder, string $filename): void
    {
        $path = $this->buildPath($folder, $filename);

        if (File::exists($path)) {
            File::delete($path);
        }
    }

    /**
     * Retourne l'URL publique d'un fichier.
     */
    public function url(string $folder, string $filename): string
    {
        return asset("assets/{$folder}/{$filename}");
    }

    /**
     * Crée le dossier s'il n'existe pas et retourne le chemin absolu.
     */
    private function ensureDirectory(string $folder): string
    {
        // Utiliser realpath pour éviter les problèmes d'espaces
        $base = rtrim(public_path(), DIRECTORY_SEPARATOR);
        $path = $base . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . $folder;

        if (! File::isDirectory($path)) {
            File::makeDirectory($path, 0755, true);
        }

        return $path;
    }

    /**
     * Construit le chemin absolu d'un fichier.
     */
    private function buildPath(string $folder, string $filename): string
    {
        return rtrim(public_path(), DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR . 'assets'
            . DIRECTORY_SEPARATOR . $folder
            . DIRECTORY_SEPARATOR . $filename;
    }

    /**
     * Génère une chaîne aléatoire courte pour éviter les collisions.
     */
    private function randomString(int $length = 6): string
    {
        return Str::lower(Str::random($length));
    }
}
