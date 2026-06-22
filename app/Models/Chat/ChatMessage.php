<?php

declare(strict_types=1);

namespace App\Models\Chat;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    const ROLE_USER        = 'user';
    const ROLE_ASSISTANT   = 'assistant';
    const ROLE_TOOL_CALL   = 'tool_call';
    const ROLE_TOOL_RESULT = 'tool_result';

    protected $fillable = [
        'session_id',
        'role',
        'content',
        'tool_name',
        'tool_input',
        'tool_result',
        'tool_call_id',
        'input_tokens',
        'output_tokens',
        'estimated_cost',
    ];

    protected $casts = [
        'tool_input'     => 'array',
        'tool_result'    => 'array',
        'input_tokens'   => 'integer',
        'output_tokens'  => 'integer',
        'estimated_cost' => 'float',
    ];

    // ── Relations ───────────────────────────────────────────

    public function session(): BelongsTo
    {
        return $this->belongsTo(ChatSession::class, 'session_id');
    }

    // ── Helpers ─────────────────────────────────────────────

    public function isUserMessage(): bool
    {
        return $this->role === self::ROLE_USER;
    }

    public function isAssistantMessage(): bool
    {
        return $this->role === self::ROLE_ASSISTANT;
    }

    public function isToolCall(): bool
    {
        return $this->role === self::ROLE_TOOL_CALL;
    }

    public function totalTokens(): int
    {
        return $this->input_tokens + $this->output_tokens;
    }
}
