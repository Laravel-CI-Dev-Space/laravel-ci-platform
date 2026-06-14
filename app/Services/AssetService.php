<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AssetService
{
    /**
     * Stores an uploaded file to public/assets/{folder}.
     * Uses file_get_contents + File::put instead of $file->move() to avoid
     * rename() failures with Livewire TemporaryUploadedFile across storage paths.
     *
     * @param  mixed  $file  UploadedFile or Livewire TemporaryUploadedFile
     * @param  string  $folder  Sub-directory under public/assets (e.g. avatars, cv)
     * @param  string  $prefix  Filename prefix (e.g. avatar, cv)
     * @param  int  $userId  Owner's user ID
     * @param  string|null  $old  Previous filename to delete
     * @return string Saved filename
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

        $destination = $this->ensureDirectory($folder);

        // Use MIME-based extension, not the client-supplied filename
        $extension = $file->extension();
        $filename  = Str::lower("{$prefix}_{$userId}_" . time() . "_{$this->randomString()}.{$extension}");

        // Read + write avoids cross-path rename() failures with Livewire temp files
        File::put(
            $destination . DIRECTORY_SEPARATOR . $filename,
            file_get_contents($file->getRealPath())
        );

        return $filename;
    }

    /** Deletes a stored file from public/assets/{folder}. */
    public function delete(string $folder, string $filename): void
    {
        $path = $this->buildPath($folder, $filename);

        if (File::exists($path)) {
            File::delete($path);
        }
    }

    /**
     * Stores an uploaded file on the private "local" disk (storage/app/private/{folder}).
     * Use this for documents that must not be publicly accessible (e.g. CVs).
     *
     * @param  mixed  $file  UploadedFile or Livewire TemporaryUploadedFile
     * @param  string  $folder  Sub-directory under storage/app/private (e.g. cv)
     * @param  string  $prefix  Filename prefix (e.g. cv)
     * @param  int  $userId  Owner's user ID
     * @param  string|null  $old  Previous filename to delete
     * @return string Saved filename
     */
    public function uploadPrivate(
        mixed $file,
        string $folder,
        string $prefix,
        int $userId,
        ?string $old = null
    ): string {
        if ($old) {
            $this->deletePrivate($folder, $old);
        }

        $extension = $file->extension();
        $filename  = Str::lower("{$prefix}_{$userId}_" . time() . "_{$this->randomString()}.{$extension}");

        Storage::disk('local')->put(
            "{$folder}/{$filename}",
            file_get_contents($file->getRealPath())
        );

        return $filename;
    }

    /** Deletes a stored file from the private "local" disk (storage/app/private/{folder}). */
    public function deletePrivate(string $folder, string $filename): void
    {
        Storage::disk('local')->delete("{$folder}/{$filename}");
    }

    /** Returns the public URL for a stored file. */
    public function url(string $folder, string $filename): string
    {
        return asset("assets/{$folder}/{$filename}");
    }

    /** Creates the target directory if it does not exist and returns its absolute path. */
    private function ensureDirectory(string $folder): string
    {
        $path = rtrim(public_path(), DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR . 'assets'
            . DIRECTORY_SEPARATOR . $folder;

        if (! File::isDirectory($path)) {
            File::makeDirectory($path, 0755, true);
        }

        return $path;
    }

    private function buildPath(string $folder, string $filename): string
    {
        return rtrim(public_path(), DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR . 'assets'
            . DIRECTORY_SEPARATOR . $folder
            . DIRECTORY_SEPARATOR . $filename;
    }

    private function randomString(int $length = 6): string
    {
        return Str::lower(Str::random($length));
    }
}
