<?php

declare(strict_types=1);

namespace App\AI\Providers;

use App\AI\Contracts\AIProviderContract;
use App\AI\DTOs\ChatPayload;
use App\AI\DTOs\ChatResponse;
use App\Models\Chat\AiModel;
use Anthropic\Client as AnthropicClient;
use Generator;

class ClaudeProvider implements AIProviderContract
{
    private AnthropicClient $client;

    public function __construct(private readonly AiModel $model)
    {
        $this->client = \Anthropic::factory()
            ->withApiKey($model->provider->api_key)
            ->make();
    }

    public function chat(ChatPayload $payload): ChatResponse
    {
        $params = $this->buildParams($payload);

        $response = $this->client->messages()->create($params);

        $content   = '';
        $toolCall  = null;
        $toolCallId = null;
        $stopReason = $response->stopReason ?? 'end_turn';

        foreach ($response->content as $block) {
            if ($block->type === 'text') {
                $content .= $block->text;
            } elseif ($block->type === 'tool_use') {
                $toolCall   = ['name' => $block->name, 'input' => (array) $block->input];
                $toolCallId = $block->id;
            }
        }

        $inputTokens  = $response->usage->inputTokens ?? 0;
        $outputTokens = $response->usage->outputTokens ?? 0;
        $cost         = $this->model->estimateCost($inputTokens, $outputTokens);

        if ($toolCall) {
            return ChatResponse::toolCall(
                $toolCall['name'],
                $toolCall['input'],
                $toolCallId,
                $inputTokens,
                $outputTokens,
                $cost,
            );
        }

        return ChatResponse::text($content, $inputTokens, $outputTokens, $cost, $stopReason);
    }

    public function stream(ChatPayload $payload): Generator
    {
        $params = $this->buildParams($payload, stream: true);

        $stream       = $this->client->messages()->stream($params);
        $fullContent  = '';
        $inputTokens  = 0;
        $outputTokens = 0;

        foreach ($stream as $event) {
            if (isset($event->type)) {
                if ($event->type === 'content_block_delta' && isset($event->delta->text)) {
                    $fullContent .= $event->delta->text;
                    yield $event->delta->text;
                } elseif ($event->type === 'message_delta' && isset($event->usage)) {
                    $outputTokens = $event->usage->outputTokens ?? 0;
                } elseif ($event->type === 'message_start' && isset($event->message->usage)) {
                    $inputTokens = $event->message->usage->inputTokens ?? 0;
                }
            }
        }

        $cost = $this->model->estimateCost($inputTokens, $outputTokens);
        yield ChatResponse::text($fullContent, $inputTokens, $outputTokens, $cost);
    }

    public function normalizeMessages(array $messages): array
    {
        $normalized = [];

        foreach ($messages as $msg) {
            $role = match ($msg['role']) {
                'user'        => 'user',
                'assistant'   => 'assistant',
                'tool_call'   => 'assistant',
                'tool_result' => 'user',
                default       => $msg['role'],
            };

            if ($msg['role'] === 'tool_call') {
                $normalized[] = [
                    'role'    => 'assistant',
                    'content' => [[
                        'type'  => 'tool_use',
                        'id'    => $msg['tool_call_id'] ?? uniqid('tool_'),
                        'name'  => $msg['tool_name'],
                        'input' => $msg['tool_input'] ?? [],
                    ]],
                ];
            } elseif ($msg['role'] === 'tool_result') {
                $normalized[] = [
                    'role'    => 'user',
                    'content' => [[
                        'type'       => 'tool_result',
                        'tool_use_id' => $msg['tool_call_id'] ?? '',
                        'content'    => is_array($msg['tool_result'])
                            ? json_encode($msg['tool_result'])
                            : (string) $msg['tool_result'],
                    ]],
                ];
            } else {
                $normalized[] = ['role' => $role, 'content' => $msg['content']];
            }
        }

        return $normalized;
    }

    public function normalizeTools(array $tools): array
    {
        return array_map(fn ($tool) => [
            'name'         => $tool['name'],
            'description'  => $tool['description'],
            'input_schema' => $tool['parameters'],
        ], $tools);
    }

    public function supports(string $feature): bool
    {
        return match ($feature) {
            'tools', 'streaming', 'vision' => true,
            default                        => false,
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

    private function buildParams(ChatPayload $payload, bool $stream = false): array
    {
        $params = [
            'model'      => $this->model->model_name,
            'max_tokens' => min($payload->maxTokens, $this->model->max_tokens),
            'system'     => $payload->systemPrompt,
            'messages'   => $this->normalizeMessages($payload->messages->toArray()),
        ];

        if (! empty($payload->tools) && $this->model->supports_tools) {
            $params['tools'] = $this->normalizeTools($payload->tools);
        }

        if ($stream) {
            $params['stream'] = true;
        }

        return $params;
    }
}
