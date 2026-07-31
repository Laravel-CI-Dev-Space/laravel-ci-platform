<?php

declare(strict_types=1);

namespace App\Livewire\Blog;

use App\Services\Blog\ResourceService;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ResourceList extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    #[Url]
    public string $type = 'all';

    public function updatedType(): void
    {
        $this->resetPage();
    }

    public function render(ResourceService $resourceService): View
    {
        $resources = $resourceService->getResources(type: $this->type);

        return view('livewire.blog.resource-list', compact('resources'));
    }
}
