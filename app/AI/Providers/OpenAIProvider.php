<?php

declare(strict_types=1);

namespace App\AI\Providers;

use App\AI\Contracts\AIProviderContract;
use App\AI\DTOs\ChatPayload;
use App\AI\DTOs\ChatResponse;
use App\Models\Chat\AiModel;
use Generator;
use OpenAI\Client as OpenAIClient;

class OpenAIProvider implements AIProviderContract
{
    protected OpenAIClient $client;

    public function __construct(protected readonly AiModel $model)
    {
        $this->client = \OpenAI::factory()
            ->withApiKey($model->provider->api_key)
            ->withBaseUri($this->resolveBaseUrl())
            ->make();
    }

    public function chat(ChatPayload $payload): ChatResponse
    {
        $params   = $this->buildParams($payload);
        $response = $this->client->chat()->create($params);
        $choice   = $response->choices[0];

        $inputTokens  = $response->usage->promptTokens ?? 0;
        $outputTokens = $response->usage->completionTokens ?? 0;
        $cost         = $this->model->estimateCost($inputTokens, $outputTokens);

        // Tool call
        if (! empty($choice->message->toolCalls)) {
            $tc = $choice->message->toolCalls[0];
            return ChatResponse::toolCall(
                $tc->function->name,
                json_decode($tc->function->arguments, true) ?? [],
                $tc->id,
                $inputTokens,
                $outputTokens,
                $cost,
            );
        }

        return ChatResponse::text(
            $choice->message->content ?? '',
            $inputTokens,
            $outputTokens,
            $cost,
            $choice->finishReason ?? 'stop',
        );
    }

    public function stream(ChatPayload $payload): Generator
    {
        $params         = $this->buildParams($payload, stream: true);
        $stream         = $this->client->chat()->createStreamed($params);
        $fullContent    = '';
        $inputTokens    = 0;
        $outputTokens   = 0;

        foreach ($stream as $response) {
            $delta = $response->choices[0]->delta ?? null;
            if ($delta && isset($delta->content) && $delta->content !== null) {
                $fullContent .= $delta->content;
                yield $delta->content;
            }
            // Les tokens ne sont disponibles qu'à la fin dans OpenAI
            if (isset($response->usage)) {
                $inputTokens  = $response->usage->promptTokens ?? 0;
                $outputTokens = $response->usage->completionTokens ?? 0;
            }
        }

        $cost = $this->model->estimateCost($inputTokens, $outputTokens);
        yield ChatResponse::text($fullContent, $inputTokens, $outputTokens, $cost);
    }

    public function normalizeMessages(array $messages): array
    {
        $normalized = [];

        foreach ($messages as $msg) {
            if ($msg['role'] === 'tool_call') {
                $normalized[] = [
                    'role'       => 'assistant',
                    'content'    => null,
                    'tool_calls' => [[
                        'id'       => $msg['tool_call_id'] ?? uniqid('call_'),
                        'type'     => 'function',
                        'function' => [
                            'name'      => $msg['tool_name'],
                            'arguments' => json_encode($msg['tool_input'] ?? []),
                        ],
                    ]],
                ];
            } elseif ($msg['role'] === 'tool_result') {
                $normalized[] = [
                    'role'         => 'tool',
                    'tool_call_id' => $msg['tool_call_id'] ?? '',
                    'content'      => is_array($msg['tool_result'])
                        ? json_encode($msg['tool_result'])
                        : (string) ($msg['tool_result'] ?? ''),
                ];
            } else {
                $normalized[] = [
                    'role'    => $msg['role'],
                    'content' => $msg['content'] ?? '',
                ];
            }
        }

        return $normalized;
    }

    public function normalizeTools(array $tools): array
    {
        return array_map(fn ($tool) => [
            'type'     => 'function',
            'function' => [
                'name'        => $tool['name'],
                'description' => $tool['description'],
                'parameters'  => $tool['parameters'],
            ],
        ], $tools);
    }

    public function supports(string $feature): bool
    {
        return match ($feature) {
            'tools', 'streaming' => true,
            'vision'             => str_contains($this->model->model_name, 'vision')
                                    || str_contains($this->model->model_name, 'gpt-4o'),
            default              => false,
        };
    }

    public function modelName(): string
    {
        return $this->model->model_name;
    }

    public function isConfigured(): bool
    {
        return ! empty($this->model->provider->api_key);
    }

    protected function resolveBaseUrl(): string
    {
        $extra = $this->model->provider->extra_config ?? [];
        return $extra['base_url'] ?? $this->model->provider->base_url;
    }

    private function buildParams(ChatPayload $payload, bool $stream = false): array
    {
        $params = [
            'model'    => $this->model->model_name,
            'messages' => array_merge(
                [['role' => 'system', 'content' => $payload->systemPrompt]],
                $this->normalizeMessages($payload->messages->toArray()),
            ),
            'max_tokens' => min($payload->maxTokens, $this->model->max_tokens),
        ];

        if (! empty($payload->tools) && $this->model->supports_tools) {
            $params['tools']       = $this->normalizeTools($payload->tools);
            $params['tool_choice'] = 'auto';
        }

        if ($stream) {
            $params['stream']         = true;
            $params['stream_options'] = ['include_usage' => true];
        }

        return $params;
    }
}
