<?php

declare(strict_types=1);

namespace App\Livewire\Jobs;

use App\Models\JobOfferCategory;
use App\Models\JobSkill;
use App\Services\Jobs\JobOfferService;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class JobList extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    #[Url]
    public string $contractType = 'all';

    #[Url]
    public string $level = 'all';

    #[Url]
    public bool $isRemote = false;

    #[Url]
    public string $location = '';

    #[Url]
    public string $search = '';

    #[Url]
    public ?int $categoryId = null;

    #[Url]
    public ?int $skillId = null;

    #[Url]
    public string $sort = 'recent';

    public function updatedContractType(): void
    {
        $this->resetPage();
    }

    public function updatedLevel(): void
    {
        $this->resetPage();
    }

    public function updatedIsRemote(): void
    {
        $this->resetPage();
    }

    public function updatedLocation(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCategoryId(): void
    {
        $this->resetPage();
    }

    public function updatedSkillId(): void
    {
        $this->resetPage();
    }

    public function updatedSort(): void
    {
        $this->resetPage();
    }

    public function render(JobOfferService $jobOfferService): View
    {
        $filters = [
            'search'        => $this->search,
            'contract_type' => $this->contractType !== 'all' ? $this->contractType : null,
            'level'         => $this->level        !== 'all' ? $this->level : null,
            'is_remote'     => $this->isRemote ?: null,
            'location'      => $this->location ?: null,
            'category_id'   => $this->categoryId,
            'skill_id'      => $this->skillId,
            'sort'          => $this->sort,
        ];

        $offers     = $jobOfferService->getOffers(array_filter($filters), 15);
        $categories = JobOfferCategory::orderBy('name')->get();
        $skills     = JobSkill::orderByDesc('offers_count')->limit(20)->get();

        return view('livewire.jobs.job-list', compact('offers', 'categories', 'skills'));
    }
}
