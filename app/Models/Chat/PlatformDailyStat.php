<?php

declare(strict_types=1);

namespace App\Models\Chat;

use Illuminate\Database\Eloquent\Model;

class PlatformDailyStat extends Model
{
    protected $fillable = [
        'date',
        'new_users',
        'active_users',
        'total_users',
        'new_questions',
        'new_answers',
        'new_articles',
        'new_comments',
        'new_job_offers',
        'new_applications',
        'new_registrations',
        'total_chat_tokens',
        'total_chat_sessions',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public static function forDate(string|\Carbon\Carbon $date): self
    {
        return static::firstOrCreate(['date' => $date]);
    }
}
