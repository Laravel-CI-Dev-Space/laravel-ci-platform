<?php

declare(strict_types=1);

namespace App\AI\DTOs;

final class ChatResponse
{
    public function __construct(
        public readonly string  $content,
        public readonly int     $inputTokens,
        public readonly int     $outputTokens,
        public readonly float   $estimatedCost,
        public readonly ?array  $toolCall = null,   // si le modèle a appelé un outil
        public readonly ?string $toolCallId = null,
        public readonly string  $stopReason = 'end_turn',
    ) {}

    public static function text(
        string $content,
        int    $inputTokens,
        int    $outputTokens,
        float  $estimatedCost,
        string $stopReason = 'end_turn',
    ): self {
        return new self($content, $inputTokens, $outputTokens, $estimatedCost, null, null, $stopReason);
    }

    public static function toolCall(
        string $toolName,
        array  $toolInput,
        string $toolCallId,
        int    $inputTokens,
        int    $outputTokens,
        float  $estimatedCost,
    ): self {
        return new self(
            '',
            $inputTokens,
            $outputTokens,
            $estimatedCost,
            ['name' => $toolName, 'input' => $toolInput],
            $toolCallId,
            'tool_use',
        );
    }

    public function isToolCall(): bool
    {
        return $this->stopReason === 'tool_use' && $this->toolCall !== null;
    }

    public function totalTokens(): int
    {
        return $this->inputTokens + $this->outputTokens;
    }
}
