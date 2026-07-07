<?php

declare(strict_types=1);

namespace App\Livewire\Chat;

use App\AI\ChatService;
use App\Models\Chat\ChatSession;
use App\Models\Chat\ChatTokenBudget;
use Livewire\Attributes\On;
use Livewire\Component;

class ChatWidget extends Component
{
    public bool   $isOpen         = false;
    public string $input          = '';
    public string $pendingMessage = '';
    public string $context        = ChatSession::CONTEXT_PUBLIC;
    public ?int   $sessionId      = null;
    public array  $messages       = [];
    public bool   $loading        = false;
    public ?array $budget         = null;
    public string $error          = '';

    public function mount(string $context = ChatSession::CONTEXT_PUBLIC): void
    {
        $this->context = $context;

        if (auth()->check()) {
            $b = ChatTokenBudget::forToday(auth()->id());
            $this->budget = [
                'used'      => $b->totalUsed(),
                'limit'     => $b->daily_limit,
                'remaining' => $b->remaining(),
                'percent'   => $b->usagePercent(),
            ];
        }
    }

    public function toggle(): void
    {
        $this->isOpen = ! $this->isOpen;
        $this->error  = '';
    }

    /**
     * Requête 1 — rapide : affiche le message utilisateur + indicateur de frappe,
     * puis déclenche processAI() via un événement browser.
     */
    public function sendMessage(): void
    {
        if (! auth()->check()) {
            $this->error = 'Connectez-vous pour utiliser l\'assistant.';
            return;
        }

        $message = trim($this->input);
        if (empty($message)) {
            return;
        }

        $this->pendingMessage = $message;
        $this->messages[]     = ['role' => 'user', 'content' => $message];
        $this->input          = '';
        $this->loading        = true;
        $this->error          = '';

        $this->dispatch('chat-scroll-bottom');
        $this->dispatch('chat-ai-process');
    }

    /**
     * Requête 2 — lente : appelle l'IA et ajoute la réponse.
     * Déclenchée par l'événement browser 'chat-ai-process' après sendMessage().
     */
    #[On('chat-ai-process')]
    public function processAI(): void
    {
        if (! auth()->check() || empty($this->pendingMessage)) {
            $this->loading = false;
            return;
        }

        try {
            /** @var ChatService $service */
            $service = app(ChatService::class);

            $result = $service->send(
                user:      auth()->user(),
                message:   $this->pendingMessage,
                context:   $this->context,
                sessionId: $this->sessionId,
            );

            $this->sessionId      = $result['session_id'];
            $this->pendingMessage = '';
            $this->messages[]     = ['role' => 'assistant', 'content' => $result['message']];

            $b = $result['budget'];
            $this->budget = [
                'used'      => $b->totalUsed(),
                'limit'     => $b->daily_limit,
                'remaining' => $b->remaining(),
                'percent'   => $b->usagePercent(),
            ];
        } catch (\RuntimeException $e) {
            $this->error = $e->getMessage();
        } catch (\Throwable $e) {
            $this->error = 'Une erreur est survenue. Veuillez réessayer.';
            \Log::error('ChatWidget error', ['error' => $e->getMessage()]);
        } finally {
            $this->loading = false;
            $this->dispatch('chat-scroll-bottom');
        }
    }

    public function newSession(): void
    {
        $this->sessionId      = null;
        $this->pendingMessage = '';
        $this->messages       = [];
        $this->error          = '';
        $this->loading        = false;
    }

    public function render()
    {
        return view('livewire.chat.chat-widget');
    }
}
