<div class="vote-col" data-vote>
    <button
        wire:click="upvote"
        class="vote-btn {{ $userVote === 1 ? 'voted-up' : '' }}"
        aria-label="Voter positivement"
    >
        <i class="fa-solid fa-chevron-up"></i>
    </button>

    <span class="vote-score {{ $score > 0 ? 'text-brand' : ($score < 0 ? 'text-danger' : '') }}">
        {{ $score }}
    </span>

    <button
        wire:click="downvote"
        class="vote-btn {{ $userVote === -1 ? 'voted-down' : '' }}"
        aria-label="Voter négativement"
    >
        <i class="fa-solid fa-chevron-down"></i>
    </button>
</div>
