<?php

declare(strict_types=1);

namespace App\Models\Chat;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiKnowledgeFile extends Model
{
    // Types de fichiers de connaissance
    const TYPE_PLATFORM = 'platform';   // Qui est Laravel CI, comment ça marche
    const TYPE_LARAVEL  = 'laravel';    // Base Laravel/PHP
    const TYPE_BEHAVIOR = 'behavior';   // Style de réponse, ton, limites

    protected $fillable = [
        'type',
        'label',
        'filename',
        'disk_path',
        'content',
        'is_active',
        'uploaded_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ── Relations ───────────────────────────────────────────

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // ── Scopes ──────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    // ── Helpers ─────────────────────────────────────────────

    public static function activeContentByType(string $type): string
    {
        return static::active()->ofType($type)->get()
            ->map(fn ($f) => $f->content)
            ->implode("\n\n---\n\n");
    }

    public static function allActiveContent(): string
    {
        $order = [self::TYPE_BEHAVIOR, self::TYPE_PLATFORM, self::TYPE_LARAVEL];

        return collect($order)
            ->map(fn ($type) => static::activeContentByType($type))
            ->filter()
            ->implode("\n\n===\n\n");
    }
}
