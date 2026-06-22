<?php

declare(strict_types=1);

namespace App\Models\Chat;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiProvider extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'base_url',
        'api_key',
        'is_active',
        'priority',
        'extra_config',
    ];

    protected $casts = [
        'api_key'      => 'encrypted',
        'is_active'    => 'boolean',
        'priority'     => 'integer',
        'extra_config' => 'array',
    ];

    protected $hidden = ['api_key'];

    // ── Relations ───────────────────────────────────────────

    public function models(): HasMany
    {
        return $this->hasMany(AiModel::class, 'provider_id');
    }

    public function activeModels(): HasMany
    {
        return $this->models()->where('is_active', true);
    }

    // ── Scopes ──────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('priority');
    }

    // ── Helpers ─────────────────────────────────────────────

    public function hasKey(): bool
    {
        return ! empty($this->api_key);
    }
}
