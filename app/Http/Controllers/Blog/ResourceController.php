<?php

declare(strict_types=1);

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Blog\StoreResourceRequest;
use App\Models\Resource;
use App\Services\Blog\ResourceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ResourceController extends Controller
{
    public function __construct(private readonly ResourceService $resourceService) {}

    /**
     * Liste paginée des ressources publiques.
     */
    public function index(): View
    {
        $resources = $this->resourceService->getResources();

        return view('web.resources.index', compact('resources'));
    }

    /**
     * Enregistre une nouvelle ressource téléchargeable.
     */
    public function store(StoreResourceRequest $request): RedirectResponse
    {
        $this->resourceService->uploadResource(
            user: $request->user(),
            data: $request->validated(),
            file: $request->file('file'),
        );

        return redirect()
            ->back()
            ->with('success', 'Votre ressource a été mise en ligne avec succès.');
    }

    /**
     * Incrémente le compteur et retourne le fichier en téléchargement.
     */
    public function download(Resource $resource): BinaryFileResponse
    {
        abort_unless(
            $resource->is_public || request()->user()?->hasAnyRole(['admin', 'moderator', 'super-admin']),
            403,
        );

        $path = $this->resourceService->download($resource);

        return response()->download($path, $resource->file_name);
    }
}
