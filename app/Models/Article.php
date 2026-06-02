<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'user_id', 'reviewed_by', 'title', 'slug', 'excerpt', 'body', 'body_html',
    'cover_image', 'level', 'status', 'rejection_reason', 'views_count',
    'comments_count', 'newsletter_sent', 'published_at', 'reviewed_at', 'edited_at',
])]
class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'published_at'    => 'datetime',
            'reviewed_at'     => 'datetime',
            'edited_at'       => 'datetime',
            'newsletter_sent' => 'boolean',
            'views_count'     => 'integer',
            'comments_count'  => 'integer',
        ];
    }

    /** L'article peut encore être modifié (moins de 48h depuis la création). */
    public function canEdit(): bool
    {
        return $this->created_at->diffInHours(now()) < 48;
    }

    /** L'article a déjà été modifié après sa création. */
    public function wasEdited(): bool
    {
        return $this->edited_at !== null;
    }

    /** Vérifie si l'utilisateur a le droit d'éditer (propriétaire dans les 48h ou admin). */
    public function canEditBy(User $user): bool
    {
        if ($user->hasAnyRole(['admin', 'moderator', 'super-admin'])) {
            return true;
        }

        return $this->isOwnedBy($user) && $this->canEdit();
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function reports(): MorphMany
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->user_id === $user->id;
    }

    public static array $levelLabels = [
        'beginner'     => 'Débutant',
        'intermediate' => 'Intermédiaire',
        'advanced'     => 'Avancé',
    ];

    public static array $levelColors = [
        'beginner'     => 'success',
        'intermediate' => 'warning',
        'advanced'     => 'danger',
    ];
}
