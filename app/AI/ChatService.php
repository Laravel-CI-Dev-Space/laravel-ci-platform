<?php

declare(strict_types=1);

namespace App\AI;

use App\AI\Contracts\AIProviderContract;
use App\AI\DTOs\ChatPayload;
use App\AI\DTOs\ChatResponse;
use App\AI\Tools\ChatToolRegistry;
use App\Models\Chat\ChatMessage;
use App\Models\Chat\ChatSession;
use App\Models\Chat\ChatTokenBudget;
use App\Models\User;
use Generator;
use RuntimeException;

class ChatService
{
    private const MAX_TOOL_ITERATIONS = 5; // évite les boucles infinies de tool calls

    public function __construct(
        private readonly AIRouter            $router,
        private readonly SystemPromptBuilder $promptBuilder,
    ) {}

    // ── Envoyer un message et obtenir une réponse complète ───

    public function send(User $user, string $message, string $context, ?int $sessionId = null): array
    {
        $this->assertBudget($user, $context);

        $model    = $this->router->resolveModelForUser($user);
        $provider = $this->router->makeProvider($model);
        $session  = $this->resolveSession($user, $context, $sessionId, $model);
        $registry = new ChatToolRegistry($user);

        // Sauvegarder le message utilisateur
        $this->saveMessage($session, ChatMessage::ROLE_USER, $message);

        // Activer les tools uniquement si le modèle ET le provider le supportent
        $tools = ($model?->supports_tools && $provider->supports('tools'))
            ? $registry->definitions()
            : [];

        // Construire le payload
        $messages = $this->buildMessageHistory($session);
        $payload  = ChatPayload::make(
            systemPrompt: $this->promptBuilder->build($user, $context),
            messages:     $messages,
            tools:        $tools,
            maxTokens:    1024,
            context:      $context,
            stream:       false,
        );

        // Boucle tool-use
        $response = $this->runWithTools($provider, $payload, $registry, $session);

        // Consommer les tokens
        ChatTokenBudget::consume($user->id, $context, $response->totalTokens());

        // Sauvegarder la réponse finale
        $this->saveMessage(
            $session,
            ChatMessage::ROLE_ASSISTANT,
            $response->content,
            inputTokens:   $response->inputTokens,
            outputTokens:  $response->outputTokens,
            estimatedCost: $response->estimatedCost,
        );

        $session->touchActivity();
        $session->generateTitle();

        return [
            'session_id' => $session->id,
            'message'    => $response->content,
            'tokens'     => $response->totalTokens(),
            'budget'     => ChatTokenBudget::forToday($user->id),
        ];
    }

    // ── Streaming ────────────────────────────────────────────

    public function stream(User $user, string $message, string $context, ?int $sessionId = null): Generator
    {
        $this->assertBudget($user, $context);

        $session  = $this->resolveSession($user, $context, $sessionId);
        $provider = $this->router->resolveForUser($user);

        $this->saveMessage($session, ChatMessage::ROLE_USER, $message);

        $messages = $this->buildMessageHistory($session);
        $payload  = ChatPayload::make(
            systemPrompt: $this->promptBuilder->build($user, $context),
            messages:     $messages,
            tools:        [],
            maxTokens:    1024,
            context:      $context,
            stream:       true,
        );

        $fullContent  = '';
        $finalResponse = null;

        foreach ($provider->stream($payload) as $chunk) {
            if ($chunk instanceof ChatResponse) {
                $finalResponse = $chunk;
            } else {
                $fullContent .= $chunk;
                yield $chunk;
            }
        }

        if ($finalResponse) {
            ChatTokenBudget::consume($user->id, $context, $finalResponse->totalTokens());
            $this->saveMessage(
                $session,
                ChatMessage::ROLE_ASSISTANT,
                $fullContent,
                inputTokens:   $finalResponse->inputTokens,
                outputTokens:  $finalResponse->outputTokens,
                estimatedCost: $finalResponse->estimatedCost,
            );
            $session->touchActivity();
            $session->generateTitle();

            yield ['done' => true, 'session_id' => $session->id, 'budget' => ChatTokenBudget::forToday($user->id)];
        }
    }

    // ── Historique d'une session ─────────────────────────────

    public function getHistory(ChatSession $session): array
    {
        return $session->messages()
            ->whereIn('role', [ChatMessage::ROLE_USER, ChatMessage::ROLE_ASSISTANT])
            ->get()
            ->map(fn ($m) => [
                'role'       => $m->role,
                'content'    => $m->content,
                'created_at' => $m->created_at->diffForHumans(),
            ])
            ->toArray();
    }

    // ── Sessions d'un user ───────────────────────────────────

    public function getSessions(User $user, string $context): \Illuminate\Support\Collection
    {
        return ChatSession::forUser($user->id)
            ->where('context', $context)
            ->with(['model.provider'])
            ->latest('last_activity_at')
            ->get();
    }

    // ── Privé ────────────────────────────────────────────────

    private function runWithTools(
        AIProviderContract $provider,
        ChatPayload $payload,
        ChatToolRegistry $registry,
        ChatSession $session,
    ): ChatResponse {
        $iterations = 0;

        while ($iterations < self::MAX_TOOL_ITERATIONS) {
            $response = $provider->chat($payload);

            if (! $response->isToolCall()) {
                return $response;
            }

            // Sauvegarder le tool_call
            $this->saveToolCall($session, $response);

            // Exécuter le tool
            $toolResult = $registry->execute(
                $response->toolCall['name'],
                $response->toolCall['input'],
            );

            // Sauvegarder le tool_result
            $this->saveToolResult($session, $response, $toolResult);

            // Réinjecter dans le payload pour la prochaine itération
            $updatedMessages = $this->buildMessageHistory($session);
            $payload = ChatPayload::make(
                systemPrompt: $payload->systemPrompt,
                messages:     $updatedMessages,
                tools:        $payload->tools,
                maxTokens:    $payload->maxTokens,
                context:      $payload->context,
                stream:       false,
            );

            $iterations++;
        }

        return ChatResponse::text(
            'Je n\'ai pas pu compléter la requête après plusieurs tentatives.',
            0, 0, 0.0
        );
    }

    private function resolveSession(User $user, string $context, ?int $sessionId, ?\App\Models\Chat\AiModel $model = null): ChatSession
    {
        if ($sessionId) {
            return ChatSession::where('id', $sessionId)
                ->where('user_id', $user->id)
                ->firstOrFail();
        }

        $model ??= $this->router->resolveModelForUser($user);

        return ChatSession::create([
            'user_id'  => $user->id,
            'model_id' => $model->id,
            'context'  => $context,
        ]);
    }

    private function buildMessageHistory(ChatSession $session): \Illuminate\Support\Collection
    {
        return $session->messages()
            ->get()
            ->map(fn ($m) => [
                'role'         => $m->role,
                'content'      => $m->content,
                'tool_name'    => $m->tool_name,
                'tool_input'   => $m->tool_input,
                'tool_result'  => $m->tool_result,
                'tool_call_id' => $m->tool_call_id,
            ]);
    }

    private function saveMessage(
        ChatSession $session,
        string      $role,
        ?string     $content,
        int         $inputTokens = 0,
        int         $outputTokens = 0,
        float       $estimatedCost = 0.0,
    ): ChatMessage {
        return ChatMessage::create([
            'session_id'     => $session->id,
            'role'           => $role,
            'content'        => $content,
            'input_tokens'   => $inputTokens,
            'output_tokens'  => $outputTokens,
            'estimated_cost' => $estimatedCost,
        ]);
    }

    private function saveToolCall(ChatSession $session, ChatResponse $response): void
    {
        ChatMessage::create([
            'session_id'   => $session->id,
            'role'         => ChatMessage::ROLE_TOOL_CALL,
            'tool_name'    => $response->toolCall['name'],
            'tool_input'   => $response->toolCall['input'],
            'tool_call_id' => $response->toolCallId,
        ]);
    }

    private function saveToolResult(ChatSession $session, ChatResponse $response, mixed $result): void
    {
        ChatMessage::create([
            'session_id'   => $session->id,
            'role'         => ChatMessage::ROLE_TOOL_RESULT,
            'tool_result'  => is_array($result) ? $result : ['value' => $result],
            'tool_call_id' => $response->toolCallId,
        ]);
    }

    private function assertBudget(User $user, string $context): void
    {
        $budget = ChatTokenBudget::forToday($user->id);

        if ($budget->isExhausted()) {
            throw new RuntimeException(
                "Limite journalière de tokens atteinte ({$budget->daily_limit} tokens). Revenez demain."
            );
        }
    }
}
