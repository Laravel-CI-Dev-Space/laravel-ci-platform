<?php

declare(strict_types=1);

namespace App\AI\DTOs;

use App\Models\Chat\ChatSession;
use Illuminate\Support\Collection;

final class ChatPayload
{
    public function __construct(
        public readonly string     $systemPrompt,
        public readonly Collection $messages,      // Collection<ChatMessage>
        public readonly array      $tools,          // définitions des tools
        public readonly int        $maxTokens,
        public readonly string     $context,        // public | dashboard
        public readonly bool       $stream = true,
    ) {}

    public static function make(
        string     $systemPrompt,
        Collection $messages,
        array      $tools = [],
        int        $maxTokens = 2048,
        string     $context = ChatSession::CONTEXT_PUBLIC,
        bool       $stream = true,
    ): self {
        return new self($systemPrompt, $messages, $tools, $maxTokens, $context, $stream);
    }
}
