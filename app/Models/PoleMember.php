<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PoleMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'pole_id', 'user_id', 'first_name', 'last_name', 'email', 'poste', 'role', 'status', 'order',
    ];

    public function pole(): BelongsTo
    {
        return $this->belongsTo(Pole::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fullName(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function isActif(): bool
    {
        return $this->status === 'actif';
    }
}
