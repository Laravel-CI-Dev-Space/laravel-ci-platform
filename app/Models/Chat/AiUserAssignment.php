<?php

declare(strict_types=1);

namespace App\Models\Chat;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiUserAssignment extends Model
{
    protected $fillable = [
        'model_id',
        'user_id',
        'role',
        'assigned_by',
    ];

    // ── Relations ───────────────────────────────────────────

    public function model(): BelongsTo
    {
        return $this->belongsTo(AiModel::class, 'model_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    // ── Scopes ──────────────────────────────────────────────

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForRole($query, string $role)
    {
        return $query->whereNull('user_id')->where('role', $role);
    }

    public function scopeGlobalDefault($query)
    {
        return $query->whereNull('user_id')->whereNull('role');
    }
}
