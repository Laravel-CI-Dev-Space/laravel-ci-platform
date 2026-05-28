<?php

namespace App\Models;

use App\Models\Comment;
use App\Models\Report;
use App\Models\Tag;
use App\Models\User;
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
    'comments_count', 'newsletter_sent', 'published_at', 'reviewed_at',
])]
class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'published_at'    => 'datetime',
            'reviewed_at'     => 'datetime',
            'newsletter_sent' => 'boolean',
            'views_count'     => 'integer',
            'comments_count'  => 'integer',
        ];
    }

    public function author(): BelongsTo    { return $this->belongsTo(User::class, 'user_id'); }
    public function reviewer(): BelongsTo  { return $this->belongsTo(User::class, 'reviewed_by'); }
    public function tags(): BelongsToMany  { return $this->belongsToMany(Tag::class); }
    public function comments(): MorphMany  { return $this->morphMany(Comment::class, 'commentable'); }
    public function reports(): MorphMany   { return $this->morphMany(Report::class, 'reportable'); }

    public function scopePublished($query)    { return $query->where('status', 'published'); }
    public function scopePending($query)      { return $query->where('status', 'pending'); }
    public function scopeByLevel($query, $level) { return $query->where('level', $level); }

    public function isPublished(): bool   { return $this->status === 'published'; }
    public function isPending(): bool     { return $this->status === 'pending'; }
    public function isOwnedBy(User $user): bool { return $this->user_id === $user->id; }

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
