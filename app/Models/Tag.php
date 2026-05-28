<?php

namespace App\Models;

use App\Models\Article;
use App\Models\Question;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name', 'slug', 'scope', 'color', 'usage_count'])]
class Tag extends Model
{
    protected function casts(): array
    {
        return ['usage_count' => 'integer'];
    }

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class);
    }

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class);
    }

    public function scopeForum($query) { return $query->whereIn('scope', ['forum', 'both']); }
    public function scopeBlog($query)  { return $query->whereIn('scope', ['blog', 'both']); }
}
