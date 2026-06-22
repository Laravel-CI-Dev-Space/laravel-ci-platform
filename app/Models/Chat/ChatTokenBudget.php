<?php

declare(strict_types=1);

namespace App\Models\Chat;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatTokenBudget extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'public_tokens_used',
        'dashboard_tokens_used',
        'daily_limit',
    ];

    protected $casts = [
        'date'                   => 'date',
        'public_tokens_used'     => 'integer',
        'dashboard_tokens_used'  => 'integer',
        'daily_limit'            => 'integer',
    ];

    // ── Relations ───────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Helpers ─────────────────────────────────────────────

    public function totalUsed(): int
    {
        return $this->public_tokens_used + $this->dashboard_tokens_used;
    }

    public function remaining(): int
    {
        return max(0, $this->daily_limit - $this->totalUsed());
    }

    public function isExhausted(): bool
    {
        return $this->remaining() === 0;
    }

    public function usagePercent(): float
    {
        if ($this->daily_limit === 0) {
            return 100.0;
        }

        return round(($this->totalUsed() / $this->daily_limit) * 100, 1);
    }

    // ── Statics ─────────────────────────────────────────────

    public static function forToday(int $userId, int $defaultLimit = 50000): self
    {
        return static::firstOrCreate(
            ['user_id' => $userId, 'date' => today()],
            ['daily_limit' => $defaultLimit]
        );
    }

    public static function consume(int $userId, string $context, int $tokens): void
    {
        $budget = static::forToday($userId);
        $field  = $context === ChatSession::CONTEXT_DASHBOARD
            ? 'dashboard_tokens_used'
            : 'public_tokens_used';

        $budget->increment($field, $tokens);
    }
}
