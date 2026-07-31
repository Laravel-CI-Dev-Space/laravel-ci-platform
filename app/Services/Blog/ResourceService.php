<?php

declare(strict_types=1);

namespace App\Services\Blog;

use App\Models\Resource;
use App\Models\User;
use App\Services\AssetService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ResourceService
{
    public function __construct(private readonly AssetService $assetService) {}

    /**
     * Récupère les ressources publiques avec filtres.
     *
     * @param  string  $type  - all/boilerplate/cheatsheet/guide/pdf/other
     */
    public function getResources(string $type = 'all', int $perPage = 12): LengthAwarePaginator
    {
        $query = Resource::where('is_public', true)->with('user');

        if ($type !== 'all') {
            $query->where('type', $type);
        }

        return $query->orderByDesc('created_at')->paginate($perPage);
    }

    /**
     * Upload et crée une ressource téléchargeable.
     * Stocke le fichier dans public/assets/resources/ via AssetService.
     */
    public function uploadResource(User $user, array $data, mixed $file): Resource
    {
        $filename = $this->assetService->upload($file, 'resources', 'resource', $user->id);

        return Resource::create([
            'user_id'         => $user->id,
            'title'           => $data['title'],
            'description'     => $data['description'] ?? null,
            'type'            => $data['type'],
            'file_path'       => $filename,
            'file_name'       => $file->getClientOriginalName(),
            'file_size'       => $file->getSize(),
            'mime_type'       => $file->getMimeType(),
            'is_public'       => $data['is_public'] ?? true,
            'downloads_count' => 0,
        ]);
    }

    /**
     * Incrémente le compteur de téléchargements et retourne le chemin absolu du fichier.
     */
    public function download(Resource $resource): string
    {
        $resource->increment('downloads_count');

        return public_path('assets/resources/' . $resource->file_path);
    }

    /**
     * Supprime une ressource et son fichier associé.
     */
    public function deleteResource(Resource $resource): void
    {
        $this->assetService->delete('resources', $resource->file_path);
        $resource->delete();
    }
}
