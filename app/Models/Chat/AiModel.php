<?php

declare(strict_types=1);

namespace App\Models\Chat;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiModel extends Model
{
    protected $fillable = [
        'provider_id',
        'model_name',
        'display_name',
        'max_tokens',
        'cost_input_per_1k',
        'cost_output_per_1k',
        'supports_tools',
        'supports_streaming',
        'is_active',
        'is_default',
    ];

    protected $casts = [
        'max_tokens'          => 'integer',
        'cost_input_per_1k'   => 'float',
        'cost_output_per_1k'  => 'float',
        'supports_tools'      => 'boolean',
        'supports_streaming'  => 'boolean',
        'is_active'           => 'boolean',
        'is_default'          => 'boolean',
    ];

    // ── Relations ───────────────────────────────────────────

    public function provider(): BelongsTo
    {
        return $this->belongsTo(AiProvider::class, 'provider_id');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(ChatSession::class, 'model_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(AiUserAssignment::class, 'model_id');
    }

    // ── Scopes ──────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->with('provider');
    }

    // ── Helpers ─────────────────────────────────────────────

    public function estimateCost(int $inputTokens, int $outputTokens): float
    {
        return ($inputTokens / 1000 * $this->cost_input_per_1k)
             + ($outputTokens / 1000 * $this->cost_output_per_1k);
    }

    public function fullName(): string
    {
        return "{$this->provider->display_name} - {$this->display_name}";
    }
}
