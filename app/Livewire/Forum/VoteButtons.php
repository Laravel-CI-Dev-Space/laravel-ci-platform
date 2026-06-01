<?php

declare(strict_types=1);

namespace App\Livewire\Forum;

use App\Models\User;
use App\Services\Forum\VoteService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

class VoteButtons extends Component
{
    public string $modelType = '';

    public int $modelId = 0;

    public int $score = 0;

    public ?int $userVote = null;

    public function mount(Model $model): void
    {
        $this->modelType = $model->getMorphClass();
        $this->modelId   = $model->id;
        $this->score     = $model->votes_score;

        if (Auth::check()) {
            /** @var User $user */
            $user           = Auth::user();
            $this->userVote = app(VoteService::class)->getUserVote($user, $model);
        }
    }

    public function upvote(): void
    {
        $this->castVote(1);
    }

    public function downvote(): void
    {
        $this->castVote(-1);
    }

    public function render(): View
    {
        return view('livewire.forum.vote-buttons');
    }

    private function castVote(int $value): void
    {
        if (! Auth::check()) {
            $this->redirect(route('login'));

            return;
        }

        $model = $this->resolveModel();

        if ($model === null) {
            return;
        }

        /** @var User $user */
        $user   = Auth::user();
        $result = app(VoteService::class)->vote($user, $model, $value);

        $this->score    = $result['score'];
        $this->userVote = $result['userVote'];
    }

    private function resolveModel(): ?Model
    {
        if (! class_exists($this->modelType)) {
            return null;
        }

        return ($this->modelType)::find($this->modelId);
    }
}
