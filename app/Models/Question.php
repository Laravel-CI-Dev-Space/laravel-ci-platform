<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string $slug
 * @property string $body
 * @property string|null $body_html
 * @property QuestionStatus $status
 * @property bool $is_pinned
 * @property int $views_count
 * @property int $votes_score
 * @property int $answers_count
 * @property int $comments_count
 * @property Carbon|null $last_activity_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User      $author
 * @property-read string    $excerpt
 */
#[Fillable(['user_id', 'title', 'slug', 'body', 'body_html', 'status', 'is_pinned'])]
class Question extends Model
{
    /** @use HasFactory<QuestionFactory> */
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'status'           => QuestionStatus::class,
            'is_pinned'        => 'boolean',
            'views_count'      => 'integer',
            'votes_score'      => 'integer',
            'answers_count'    => 'integer',
            'comments_count'   => 'integer',
            'last_activity_at' => 'datetime',
        ];
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

    public function hasAcceptedAnswer(): bool
    {
        return $this->accepted_answer_id !== null;
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->user_id === $user->id;
    }
}
