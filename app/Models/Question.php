<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Forum\QuestionStatus;
use Carbon\Carbon;
use Database\Factories\QuestionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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

    public static function boot()
    {
        parent::boot();

        static::creating(function (Question $question) {
            $question->slug = Str::slug($question->title);
        });

        static::updating(function (Question $question) {
            $question->slug = Str::slug($question->title);
        });
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    protected function excerpt(): Attribute
    {
        return Attribute::make(
            get: fn (): string => Str::limit(strip_tags($this->body), 150),
        );
    }

    /** @param Builder<Question> $query */
    public function scopePinned(Builder $query): Builder
    {
        return $query->where('is_pinned', true);
    }

    /** @param Builder<Question> $query */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', QuestionStatus::Published);
    }

    /** @param Builder<Question> $query */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', '!=', QuestionStatus::Closed);
    }

    /** @param Builder<Question> $query */
    public function scopeClosed(Builder $query): Builder
    {
        return $query->where('status', QuestionStatus::Closed);
    }

    /** @param Builder<Question> $query */
    public function scopeByRecent(Builder $query): Builder
    {
        $query->orderByDesc('is_pinned')
            ->orderByRaw('COALESCE(last_activity_at, created_at) DESC');

        return $query;
    }

    /** @param Builder<Question> $query */
    public function scopeByPopular(Builder $query): Builder
    {
        return $query->orderByDesc('is_pinned')->orderByDesc('votes_score');
    }

    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    public static function generateSlug(string $title): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i    = 1;

        while (static::withTrashed()->where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
}
