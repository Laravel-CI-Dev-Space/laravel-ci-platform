<?php

namespace App\Models;

use App\Enums\ResourceType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['user_id', 'title', 'description', 'type', 'file_path', 'file_name', 'file_size', 'mime_type', 'downloads_count', 'is_public'])]
class Resource extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'is_public'       => 'boolean',
            'file_size'       => 'integer',
            'downloads_count' => 'integer',
            'type'            => ResourceType::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fileUrl(): string
    {
        return asset('assets/resources/' . $this->file_path);
    }

    public function fileSizeHuman(): string
    {
        $bytes = $this->file_size;
        if ($bytes < 1024) {
            return "{$bytes} B";
        }
        if ($bytes < 1048576) {
            return round($bytes / 1024, 1) . ' KB';
        }

        return round($bytes / 1048576, 1) . ' MB';
    }
}
