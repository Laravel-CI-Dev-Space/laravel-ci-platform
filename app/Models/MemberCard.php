<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberCard extends Model
{
    protected $fillable = [
        'user_id',
        'level',
        'card_avatar',
        'poste',
        'qr_code_svg',
        'is_active',
        'forced_by_admin',
        'activated_by',
        'activated_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active'       => 'boolean',
            'forced_by_admin' => 'boolean',
            'activated_at'    => 'datetime',
        ];
    }

    // ─── Relations ────────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function activatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'activated_by');
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    /** Nom du niveau affiché sur la carte. */
    public function levelName(): string
    {
        return config('member-card.level_names')[$this->level] ?? 'Membre';
    }

    /**
     * Vue Blade à utiliser pour ce niveau de carte.
     * Changer le mapping dans config/member-card.php pour swapper le design.
     */
    public function templateView(): string
    {
        return config('member-card.templates')[$this->level]
            ?? 'member-card.templates.level-1';
    }

    /** URL publique du profil du membre (cible du QR code). */
    public function profileUrl(): string
    {
        return route('members.show', $this->user->github_username);
    }

    /** Avatar effectif : celui de la carte en priorité, sinon le profil. */
    public function resolvedAvatar(): ?string
    {
        return $this->card_avatar ?? $this->user->avatar;
    }

    /** Grade actuel du membre (depuis son profil). */
    public function gradeName(): string
    {
        return $this->user->profile?->grade?->name ?? 'Membre';
    }

    /** Seuil de réputation du prochain niveau (null si déjà au max). */
    public function nextThreshold(): ?int
    {
        $next = $this->level + 1;
        return config('member-card.thresholds')[$next] ?? null;
    }

    // ─── Activation ───────────────────────────────────────────────────────────

    public function activate(?int $byUserId = null, bool $forced = false): void
    {
        $this->update([
            'is_active'       => true,
            'forced_by_admin' => $forced,
            'activated_by'    => $byUserId,
            'activated_at'    => now(),
        ]);
    }

    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForLevel($query, int $level)
    {
        return $query->where('level', $level);
    }
}
