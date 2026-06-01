<?php

declare(strict_types=1);

namespace App\Livewire\Forum;

use App\Models\Tag;
use App\Services\Forum\QuestionService;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class QuestionList extends Component
{
    use WithPagination;

    #[Url]
    public string $sort = 'recent';

    #[Url]
    public ?int $tagId = null;

    #[Url]
    public string $search = '';

    public function updatedSort(): void
    {
        $this->resetPage();
    }

    public function updatedTagId(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render(QuestionService $questionService): View
    {
        $questions = $questionService->getQuestions(
            sort: $this->sort,
            tagId: $this->tagId,
            search: $this->search,
        );

        $tags = Tag::whereIn('scope', ['forum', 'both'])
            ->orderByDesc('usage_count')
            ->get();

        return view('livewire.forum.question-list', compact('questions', 'tags'));
    }
}
