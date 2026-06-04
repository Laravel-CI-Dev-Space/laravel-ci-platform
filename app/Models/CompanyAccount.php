<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'company_id', 'first_name', 'last_name', 'email', 'password',
    'position', 'phone', 'status', 'rejection_reason',
    'password_changed_at', 'last_login_at',
])]
#[Hidden(['password', 'remember_token'])]
class CompanyAccount extends Authenticatable
{
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'password'            => 'hashed',
            'password_changed_at' => 'datetime',
            'last_login_at'       => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Attribut virtuel `name` requis par Filament pour afficher le menu utilisateur.
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => "{$this->first_name} {$this->last_name}",
        );
    }

    /** Nom complet du responsable. */
    public function fullName(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /** Retourne true si le mot de passe temporaire n'a pas encore été changé. */
    public function mustChangePassword(): bool
    {
        return $this->password_changed_at === null;
    }

    public function hasCompany(): bool
    {
        return $this->company_id !== null;
    }
}
