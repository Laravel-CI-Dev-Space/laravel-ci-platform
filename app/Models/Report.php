<?php

namespace App\Models;

use App\Enums\ReportStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable([
    'reporter_id', 'reportable_id', 'reportable_type', 'reason',
    'description', 'status', 'handled_by', 'admin_note', 'handled_at',
])]
class Report extends Model
{
    protected function casts(): array
    {
        return [
            'handled_at' => 'datetime',
            'status'     => ReportStatus::class,
        ];
    }

    public function reportable(): MorphTo
    {
        return $this->morphTo();
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function isPending(): bool
    {
        return $this->status === ReportStatus::Pending;
    }

    public function isResolved(): bool
    {
        return $this->status === ReportStatus::Resolved;
    }
}
