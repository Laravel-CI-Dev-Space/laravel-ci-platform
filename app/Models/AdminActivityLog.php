<?php
namespace App\Models;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id', 'action', 'subject_type', 'subject_id',
    'description', 'old_values', 'new_values', 'metadata',
    'ip_address', 'user_agent',
])]
class AdminActivityLog extends Model
{
    public $timestamps = false;
    public $updatedAt  = false;

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
            'metadata'   => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }

    /**
     * Log an admin action easily from anywhere.
     */
    public static function log(string $action, ?Model $subject = null, array $extra = []): self
    {
        return self::create([
            'user_id'      => auth()->id(),
            'action'       => $action,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id'   => $subject?->id,
            'description'  => $extra['description'] ?? null,
            'old_values'   => $extra['old'] ?? null,
            'new_values'   => $extra['new'] ?? null,
            'metadata'     => $extra['meta'] ?? null,
            'ip_address'   => request()->ip(),
            'user_agent'   => request()->userAgent(),
        ]);
    }
}
