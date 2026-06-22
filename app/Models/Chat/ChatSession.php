<?php

declare(strict_types=1);

namespace App\Models\Chat;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ChatSession extends Model
{
    const CONTEXT_PUBLIC    = 'public';
    const CONTEXT_DASHBOARD = 'dashboard';

    protected $fillable = [
        'user_id',
        'model_id',
        'context',
        'title',
        'last_activity_at',
    ];

    protected $casts = [
        'last_activity_at' => 'datetime',
    ];

    // ── Relations ───────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function model(): BelongsTo
    {
        return $this->belongsTo(AiModel::class, 'model_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'session_id')->orderBy('id');
    }

    // ── Scopes ──────────────────────────────────────────────

    public function scopePublic($query)
    {
        return $query->where('context', self::CONTEXT_PUBLIC);
    }

    public function scopeDashboard($query)
    {
        return $query->where('context', self::CONTEXT_DASHBOARD);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // ── Helpers ─────────────────────────────────────────────

    public function totalTokens(): int
    {
        return (int) $this->messages()
            ->selectRaw('(input_tokens + output_tokens) as t')
            ->get()
            ->sum('t');
    }

    public function touchActivity(): bool
    {
        $this->last_activity_at = now();
        return $this->save();
    }

    public function generateTitle(): void
    {
        if (! $this->title) {
            $first = $this->messages()->where('role', 'user')->first();
            if ($first) {
                $this->update(['title' => Str::limit($first->content, 60)]);
            }
        }
    }
}
