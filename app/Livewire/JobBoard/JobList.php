<?php

declare(strict_types=1);

namespace App\Livewire\JobBoard;

use App\Enums\Jobs\JobOfferType;
use App\Models\JobCategory;
use App\Models\JobOffer;
use App\Models\JobSkill;
use App\Queries\Jobs\JobOfferListQuery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class JobList extends Component
{
    use WithPagination;

    #[Url(as: 'type', history: true)]
    public ?string $type = null;

    #[Url(as: 'remote', history: true)]
    public bool $remote = false;

    #[Url(as: 'skill', history: true)]
    public ?string $skill = null;

    #[Url(as: 'category', history: true)]
    public ?string $category = null;

    #[Url(as: 'sort', history: true)]
    public string $sort = 'newest';

    public function updatedType(): void
    {
        $this->resetPage();
    }

    public function updatedRemote(): void
    {
        $this->resetPage();
    }

    public function updatedSkill(): void
    {
        $this->resetPage();
    }

    public function updatedCategory(): void
    {
        $this->resetPage();
    }

    public function updatedSort(): void
    {
        $this->resetPage();
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
        $this->resetPage();
    }

    public function setRemote(bool $remote): void
    {
        $this->remote = $remote;
        $this->resetPage();
    }

    public function setSkill(?string $skillSlug): void
    {
        $this->skill = $skillSlug;
        $this->resetPage();
    }

    public function setSort(string $sort): void
    {
        if (! in_array($sort, ['newest', 'title'], true)) {
            return;
        }

        $this->sort = $sort;
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['type', 'remote', 'skill', 'category', 'sort']);
        $this->sort = 'newest';
        $this->resetPage();
    }

    #[On('job-favorite-toggled')]
    public function refreshOffers(): void
    {
        unset($this->offers);
    }

    /**
     * @return LengthAwarePaginator<int, JobOffer>
     */
    #[Computed]
    public function offers(): LengthAwarePaginator
    {
        return JobOfferListQuery::make(
            $this->type,
            $this->remote,
            $this->skill,
            $this->category,
            $this->sort,
            auth()->id(),
        )->paginate(12);
    }

    /**
     * @return Collection<int, JobSkill>
     */
    #[Computed]
    public function skills(): Collection
    {
        return JobSkill::query()->orderBy('name')->get();
    }

    /**
     * @return Collection<int, JobCategory>
     */
    #[Computed]
    public function categories(): Collection
    {
        return JobCategory::query()->orderBy('name')->get();
    }

    /**
     * @return list<JobOfferType>
     */
    #[Computed]
    public function offerTypes(): array
    {
        return JobOfferType::cases();
    }

    public function render(): View
    {
        return view('livewire.job-board.job-list');
    }
}
