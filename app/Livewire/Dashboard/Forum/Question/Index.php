<?php

declare(strict_types=1);

namespace App\Livewire\Dashboard\Forum\Question;

use App\Models\Question;
use Illuminate\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dashboard')]
#[Title('Forum')]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q', history: true)]
    public string $search = '';

    #[Url(history: true)]
    public string $sort = 'recent';

    public function openCreateDrawer(): void
    {
        $this->dispatch('open-create-drawer');
    }

    #[On('question-created')]
    public function refresh(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSort(): void
    {
        $this->resetPage();
    }

    /**
     * @return LengthAwarePaginator<Question>
     */
    private function getQuestions(): LengthAwarePaginator
    {
        return Question::query()
            ->with('author')
            ->when(
                $this->search !== '',
                fn ($q) => $q->where('title', 'like', "%{$this->search}%")
                    ->orWhere('content', 'like', "%{$this->search}%")
            )
            ->when($this->sort === 'recent', fn ($q) => $q->byRecent())
            ->when($this->sort === 'popular', fn ($q) => $q->byPopular())
            ->when($this->sort === 'unanswered', fn ($q) => $q->open()->byRecent())
            ->paginate(15);
    }

    public function render(): View
    {
        return view('livewire.dashboard.forum.question.index', [
            'questions' => $this->getQuestions(),
        ]);
    }
}
