<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'user_id', 'title', 'slug', 'body', 'body_html', 'status',
    'is_pinned', 'accepted_answer_id', 'views_count', 'votes_score',
    'answers_count', 'comments_count', 'last_activity_at', 'edited_at',
])]
class Question extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'is_pinned'        => 'boolean',
            'last_activity_at' => 'datetime',
            'edited_at'        => 'datetime',
            'views_count'      => 'integer',
            'votes_score'      => 'integer',
            'answers_count'    => 'integer',
            'comments_count'   => 'integer',
        ];
    }

    /** La question peut encore être modifiée (moins de 48h depuis la création). */
    public function canEdit(): bool
    {
        return $this->created_at->diffInHours(now()) < 48;
    }

    /** La question a été modifiée après sa publication initiale. */
    public function wasEdited(): bool
    {
        return $this->edited_at !== null;
    }

    /** Vérifie si l'utilisateur a le droit d'éditer (propriétaire dans les 48h ou admin). */
    public function canEditBy(User $user): bool
    {
        if ($user->hasAnyRole([UserRole::Admin->value, UserRole::Moderator->value, UserRole::SuperAdmin->value])) {
            return true;
        }

        return $this->isOwnedBy($user) && $this->canEdit();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function acceptedAnswer(): BelongsTo
    {
        return $this->belongsTo(Answer::class, 'accepted_answer_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function votes(): MorphMany
    {
        return $this->morphMany(Vote::class, 'votable');
    }

    public function reports(): MorphMany
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    public function scopeUnanswered($query)
    {
        return $query->where('answers_count', 0);
    }

    public function scopePopular($query)
    {
        return $query->orderByDesc('votes_score');
    }

    public function scopeRecent($query)
    {
        return $query->orderByDesc('last_activity_at');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function hasAcceptedAnswer(): bool
    {
        return $this->accepted_answer_id !== null;
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->user_id === $user->id;
    }
}
