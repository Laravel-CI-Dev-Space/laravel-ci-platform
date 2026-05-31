<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\QuestionFactory;
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
 * @property string $content
 * @property bool $pinned
 * @property bool $closed
 * @property int $views
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User           $author
 * @property-read string         $excerpt
 */
#[Fillable(['user_id', 'title', 'slug', 'content', 'pinned', 'closed', 'views'])]
class Question extends Model
{
    /** @use HasFactory<QuestionFactory> */
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'pinned' => 'boolean',
            'closed' => 'boolean',
            'views'  => 'integer',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */
    protected function excerpt(): Attribute
    {
        return Attribute::make(
            get: fn (): string => Str::limit(strip_tags($this->content), 150),
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * @param  Builder<Question>  $query
     * @return Builder<Question>
     */
    public function scopePinned(Builder $query): Builder
    {
        return $query->where('pinned', true);
    }

    /**
     * @param  Builder<Question>  $query
     * @return Builder<Question>
     */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('closed', false);
    }

    /**
     * @param  Builder<Question>  $query
     * @return Builder<Question>
     */
    public function scopeClosed(Builder $query): Builder
    {
        return $query->where('closed', true);
    }

    /**
     * Tri : épinglées en tête, puis par date décroissante.
     *
     * @param  Builder<Question>  $query
     * @return Builder<Question>
     */
    public function scopeByRecent(Builder $query): Builder
    {
        return $query->orderByDesc('pinned')->orderByDesc('created_at');
    }

    /**
     * Tri : épinglées en tête, puis par nombre de vues décroissant.
     *
     * @param  Builder<Question>  $query
     * @return Builder<Question>
     */
    public function scopeByPopular(Builder $query): Builder
    {
        return $query->orderByDesc('pinned')->orderByDesc('views');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function incrementViews(): void
    {
        $this->increment('views');
    }

    /**
     * Génère un slug unique en ajoutant un suffixe numérique si nécessaire.
     */
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
