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
    public bool   $isOpen     = false;
    public string $input      = '';
    public string $context    = ChatSession::CONTEXT_PUBLIC;
    public ?int   $sessionId  = null;
    public array  $messages   = [];
    public bool   $loading    = false;
    public ?array $budget     = null;
    public string $error      = '';

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

        $this->messages[] = [
            'role'    => 'user',
            'content' => $message,
        ];

        $this->input   = '';
        $this->loading = true;
        $this->error   = '';

        $this->dispatch('chat-scroll-bottom');

        try {
            /** @var ChatService $service */
            $service = app(ChatService::class);

            $result = $service->send(
                user:      auth()->user(),
                message:   $message,
                context:   $this->context,
                sessionId: $this->sessionId,
            );

            $this->sessionId  = $result['session_id'];
            $this->messages[] = [
                'role'    => 'assistant',
                'content' => $result['message'],
            ];

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
        $this->sessionId = null;
        $this->messages  = [];
        $this->error     = '';
    }

    public function render()
    {
        return view('livewire.chat.chat-widget');
    }
}
